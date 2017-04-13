<?php
/**
 * This file is part of the TeamNeustaGmbH/m2t3 package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/BSD-3-Clause  BSD-3-Clause License
 */

declare(strict_types=1);

namespace TeamNeustaGmbH\M2T3\Elastictypo\Hook;

use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model\ContentDocument;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service;
use TYPO3\CMS\Core\DataHandling\DataHandler;

class ElasticContentSaveHook
{
    /**
     * elasticService
     *
     * @var ElasticService
     */
    protected $elasticService;

    /**
     * typo3Services
     *
     * @var \TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service
     */
    protected $typo3Services;

    function __construct()
    {
        $this->injectElasticService(new ElasticService());
        $this->injectTypo3Service(new Typo3Service());
    }

    /**
     * injectElasticService
     *
     * @param \TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService $elasticService
     * @return void
     */
    public function injectElasticService(\TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * injectTypo3Service
     *
     * @param \TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service $typo3Services
     * @return void
     */
    public function injectTypo3Service(\TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service $typo3Services)
    {
        $this->typo3Services = $typo3Services;
    }

    /**
     * DataHandler hook function for on-the-fly indexing of database records
     *
     * @param string $status Status "new" or "update
     * @param string $table Table name
     * @param string $id Record ID. If new record its a string pointing to index inside \TYPO3\CMS\Core\DataHandling\DataHandler::substNEWwithIDs
     * @param array $fieldArray Field array of updated fields in the operation
     * @param DataHandler $pObj DataHandler calling object
     * @return void
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $pObj)
    {
        if ($table === 'tt_content') {
            if (strpos($id, 'NEW') !== false) {
                $id = $pObj->substNEWwithIDs[$id];
            }
            $currentRecord = $this->typo3Services->backendUtilityGetRecord($table, $id);

            if (!empty($currentRecord['header'])) {
                $contentModel = new ContentDocument();
                $contentModel->setContentId($currentRecord['uid']);
                $contentModel->setContentTitle($currentRecord['header']);
                $contentModel->setContentBodytexts((string)$currentRecord['bodytext']);
                $contentModel->setContentLink('content_link');
                $contentModel->setContentType($currentRecord['CType']);
                $contentModel->setPageId($currentRecord['pid']);

                $this->elasticService->addDocument($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['index'],
                    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['type'], $contentModel,
                    'tt_content_' . $currentRecord['uid']);
            }
        }
    }

    /**
     * DataHandler hook function for on-the-fly indexing of database records
     *
     * @param DataHandler $pObj DataHandler calling object
     * @return void
     */
    public function processCmdmap_postProcess($command, $table, $id, $value, $obj, $pasteUpdate, $pasteDatamap)
    {
        if ($command === 'delete' && $table === 'tt_content') {
            $this->elasticService->deleteDocument($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['index'],
                $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['type'], 'tt_content_' . $id);
        }
    }
}

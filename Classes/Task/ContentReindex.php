<?php
declare(strict_types = 1);

namespace TeamNeustaGmbH\M2T3\Elastictypo\Task;

/**
 * This file is part of the TeamNeustaGmbH/m2t3 package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/BSD-3-Clause  BSD-3-Clause License
 */

use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model\ContentDocument;
use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Repository\ContentRepository;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class ContentReindex extends AbstractTask
{
    /**
     * ContentReindex constructor.
     * @param bool $skip
     */
    public function __construct($skip = false)
    {
        if (!$skip) {
            parent::__construct();
        }
    }

    /**
     * This is the main method that is called when a task is executed
     * It MUST be implemented by all classes inheriting from this one
     * Note that there is no error handling, errors and failures are expected
     * to be handled and logged by the client implementations.
     * Should return TRUE on successful execution, FALSE on error.
     *
     * @return bool Returns TRUE on successful execution, FALSE on error
     */
    public function execute(): bool
    {
        $index = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['index'];
        $type = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['type'];

        GeneralUtility::makeInstance(Typo3Service::class)->initTSFE();

        $elasticService = GeneralUtility::makeInstance(ElasticService::class);
        $elasticService->cleanIndex($index);

        $contents = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(ContentRepository::class)
            ->findAll();

        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        try {
            foreach ($contents as $content) {
                if (strpos($content->getContentType(), 'magentypo') === false) {
                    $conf = [
                        'tables' => 'tt_content',
                        'source' => $content->getUid(),
                        'dontCheckPid' => 1,
                    ];

                    $html = $cObj->cObjGetSingle('RECORDS', $conf);
                    if ($html) {
                        $content->setBodytext(preg_replace('/\s+/', ' ', trim(strip_tags($html))));
                    }

                    $contentModel = new ContentDocument();
                    $contentModel->setContentId($content->getUid());
                    $contentModel->setContentTitle($content->getHeader());
                    $contentModel->setContentBodytexts($content->getBodytext());
                    $contentModel->setContentLink('content_link');
                    $contentModel->setContentType($content->getContentType());
                    $contentModel->setPageId($content->getPid());

                    $elasticService->addDocument(
                        $index,
                        $type,
                        $contentModel,
                        'tt_content_' . $content->getUid()
                    );
                }
            }
        } catch (\Exception $exception) {
            throw new \RuntimeException(
                __CLASS__ . ' failed for: "tt_content:' . $content->getUid() . '": ' . $exception->getMessage(),
                1578582236
            );
        }

        return true;
    }
}

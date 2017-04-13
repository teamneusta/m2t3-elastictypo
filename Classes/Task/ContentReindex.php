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

namespace TeamNeustaGmbH\M2T3\Elastictypo\Task;

use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model\Content;
use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model\ContentDocument;
use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Repository\ContentRepository;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class ContentReindex extends AbstractTask
{

    /**
     * contentRepository
     *
     * @var \TeamNeustaGmbH\M2T3\Elastictypo\Domain\Repository\ContentRepository
     */
    protected $contentRepository;

    /**
     * elasticService
     *
     * @var ElasticService
     */
    protected $elasticService;

    /**
     * contentObjectRenderer
     *
     * @var ContentObjectRenderer
     */
    protected $contentObjectRenderer;

    /**
     * typo3Service
     *
     * @var Typo3Service
     */
    protected $typo3Service;

    /**
     * ContentReindex constructor.
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
    public function execute()
    {
        $this->init();

        $this->elasticService->cleanIndex($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['index']);
        $contents = $this->contentRepository->findAll();

        /** @var Content $content */
        foreach ($contents as $content) {

            if (strpos($content->getContentType(), 'magentypo') === false) {
                $conf = [
                    'tables'       => 'tt_content',
                    'source'       => $content->getUid(),
                    'dontCheckPid' => 1
                ];

                $html = $this->contentObjectRenderer->cObjGetSingle('RECORDS', $conf);
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

                $this->elasticService->addDocument($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['index'],
                    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['type'], $contentModel,
                    'tt_content_' . $content->getUid());
            }

        }


        return true;
    }

    /**
     * init
     *
     * @return void
     */
    protected function init()
    {
        $this->injectTypo3Service();
        $this->typo3Service->initTSFE();
        $this->injectContentRepository();
        $this->injectElasticService();
        $this->injectContentObjectRenderer();
    }

    /**
     * injectTypo3Service
     *
     * @param \TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service $typo3Service
     * @return void
     */
    public function injectTypo3Service(
        \TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service $typo3Service = null
    ) {
        $this->typo3Service = $typo3Service ?: $this->typo3Service ?: new Typo3Service();
    }

    /**
     * injectContentRepository
     *
     * @param \TeamNeustaGmbH\M2T3\Elastictypo\Domain\Repository\ContentRepository|null $contentRepository
     * @return void
     */
    public function injectContentRepository(
        \TeamNeustaGmbH\M2T3\Elastictypo\Domain\Repository\ContentRepository $contentRepository = null
    ) {
        $this->contentRepository = $contentRepository ?: $this->typo3Service->objectManagerGet(
            ContentRepository::class
        );
    }

    /**
     * injectElasticService
     *
     * @param \TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService $elasticService
     * @return void
     */
    public function injectElasticService(\TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService $elasticService = null)
    {
        $this->elasticService = $elasticService ?: $this->typo3Service->objectManagerGet(ElasticService::class);
    }

    /**
     * injectContentObjectRenderer
     *
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer
     * @return void
     */
    public function injectContentObjectRenderer(
        \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer = null
    ) {
        $this->contentObjectRenderer = $contentObjectRenderer ?: $this->typo3Service->objectManagerGet(
            ContentObjectRenderer::class
        );
    }
}
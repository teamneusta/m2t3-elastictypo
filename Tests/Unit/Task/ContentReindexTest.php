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

declare(strict_types = 1);
/***************************************************************
 *  (c) 2016 Benjamin Kluge <b.kluge@neusta.de>, NEUSTA GmbH
 *  All rights reserved
 ***************************************************************/
namespace TeamNeustaGmbH\M2T3\Elastictypo\Tests\Unit\Task;


use Prophecy\Argument;
use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model\Content;
use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model\ContentDocument;
use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Repository\ContentRepository;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service;
use TeamNeustaGmbH\M2T3\Elastictypo\Task\ContentReindex;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class ContentReindexTest
 *
 * @backupGlobals true
 * @package TeamNeustaGmbH\M2T3\Elastictypo\Tests\Unit\Task
 */
class ContentReindexTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * contentReindex
     *
     * @var ContentReindex
     */
    protected $contentReindex;

    /**
     * typo3service
     *
     * @var Typo3Service
     */
    protected $typo3service;

    /**
     * contentRepository
     *
     * @var ContentRepository
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
     * executeShouldAddDocument
     *
     * @test
     * @return void
     */
    public function executeShouldAddDocumentAndReturnTrue()
    {
        $this->typo3service->initTSFE()->shouldBeCalled();
        $this->elasticService->cleanIndex('typo3')->shouldBeCalled();

        $content = new Content();
        $content->setUid(1);
        $content->setPid(2);
        $content->setHeader('someHeader');
        $content->setBodytext('someBodytext');
        $content->setContentType('someContentType');
        $this->contentRepository->findAll()->willReturn(
            [
                $content
            ]
        );

        $this->contentObjectRenderer->cObjGetSingle(
            Argument::exact('RECORDS'),
            Argument::exact(
                [
                    'tables' => 'tt_content',
                    'source' => $content->getUid(),
                    'dontCheckPid' => 1
                ]
            )
        )->willReturn('<span>some text</span> <p>with tags</p>');

        $this->elasticService
            ->addDocument(
                Argument::exact('typo3'),
                Argument::exact('content'),
                Argument::type(ContentDocument::class),
                Argument::exact('tt_content_'.$content->getUid())
            )
            ->shouldBeCalled();

        $this->assertTrue($this->contentReindex->execute());
        $this->assertSame('some text with tags', $content->getBodytext());
    }

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['index'] = 'typo3';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['type'] = 'content';
        $this->contentReindex = new ContentReindex(true);
        $this->typo3service = $this->prophesize(Typo3Service::class);
        $this->contentRepository = $this->prophesize(ContentRepository::class);
        $this->elasticService = $this->prophesize(ElasticService::class);
        $this->contentObjectRenderer = $this->prophesize(ContentObjectRenderer::class);

        $this->contentReindex->injectTypo3Service($this->typo3service->reveal());

        $this->typo3service->objectManagerGet(Argument::exact(ContentRepository::class))->willReturn(
            $this->contentRepository->reveal()
        );
        $this->typo3service->objectManagerGet(Argument::exact(ElasticService::class))->willReturn(
            $this->elasticService->reveal()
        );
        $this->typo3service->objectManagerGet(Argument::exact(ContentObjectRenderer::class))->willReturn(
            $this->contentObjectRenderer->reveal()
        );
    }
}
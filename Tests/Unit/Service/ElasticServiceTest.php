<?php
declare(strict_types = 1);

/**
 * This file is part of the TeamNeustaGmbH/m2t3 package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/BSD-3-Clause  BSD-3-Clause License
 */

namespace TeamNeustaGmbH\M2T3\Elastictypo\Tests\Unit\Hook;

use Elastica\Client;
use Elastica\Document;
use Elastica\Index;
use Elastica\Type;
use Prophecy\Argument;
use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model\ContentDocument;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ElasticServiceTest extends UnitTestCase
{

    /**
     * elasticService
     *
     * @var ElasticService
     */
    protected $elasticService;

    /**
     * client
     *
     * @var Client
     */
    protected $client;

    /**
     * index
     *
     * @var Index
     */
    protected $index;

    /**
     * type
     *
     * @var Type
     */
    protected $type;

    protected function setUp()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['index'] = 'typo3';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['type'] = 'content';
        $this->elasticService = new ElasticService();
        $this->client = $this->prophesize(Client::class);
        $this->index = $this->prophesize(Index::class);
        $this->type = $this->prophesize(Type::class);
        $this->elasticService->setClient($this->client->reveal());
    }

    /**
     * addDocumentShouldAddDocumentToElastic
     *
     * @test
     * @return void
     */
    public function addDocumentShouldAddDocumentToElastic()
    {
        $this->index->getType('content')->willReturn($this->type->reveal())->shouldBeCalled();
        $this->client->getIndex('typo3')->willReturn($this->index->reveal())->shouldBeCalled();
        $this->type->addDocument(Argument::type(Document::class))->shouldBeCalled();
        $this->type->getIndex()->willReturn($this->index->reveal())->shouldBeCalled();
        $this->index->refresh()->shouldBeCalled();


        $document = new ContentDocument();
        $document->setContentId(1);
        $document->setContentTitle('title');
        $document->setContentBodytexts('bodytext');
        $document->setContentLink('content_link');
        $document->setContentType('content typo3');
        $document->setPageId(2);

        $this->elasticService->addDocument('typo3', 'content', $document, 'tt_content_1');
    }

    /**
     * cleanIndexShouldDeleteAndRecreateIndex
     *
     * @test
     * @return void
     */
    public function cleanIndexShouldDeleteAndRecreateIndex()
    {
        $this->client->getIndex(Argument::exact('typo3'))->shouldBeCalledTimes(3)->willReturn($this->index);
        $this->index->exists()->shouldBeCalled()->willReturn(true);
        $this->index->delete()->shouldBeCalled();
        $this->index->create()->shouldBeCalled();

        $this->elasticService->cleanIndex('typo3');
    }
}

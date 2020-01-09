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

use Prophecy\Argument;
use TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model\ContentDocument;
use TeamNeustaGmbH\M2T3\Elastictypo\Hook\ElasticContentSaveHook;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\ElasticService;
use TeamNeustaGmbH\M2T3\Elastictypo\Service\Typo3Service;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ElasticContentSaveHookTest extends UnitTestCase
{

    /**
     * elasticContentSaveHook
     *
     * @var ElasticContentSaveHook
     */
    protected $elasticContentSaveHook;

    /**
     * elasticService
     *
     * @var ElasticService
     */
    protected $elasticService;

    /**
     * typo3Service
     *
     * @var Typo3Service
     */
    protected $typo3Service;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['index'] = 'typo3';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['type'] = 'content';

        $this->elasticContentSaveHook = new ElasticContentSaveHook();
        $this->elasticService = $this->prophesize(ElasticService::class);
        GeneralUtility::addInstance(ElasticService::class, $this->elasticService->reveal());

        $this->typo3Service = $this->prophesize(Typo3Service::class);
        GeneralUtility::addInstance(Typo3Service::class, $this->typo3Service->reveal());
    }

    /**
     * processDatamap_afterDatabaseOperationsShouldAddDocument
     *
     * @test
     * @return void
     */
    public function processDatamap_afterDatabaseOperationsShouldAddDocument()
    {
        $this->elasticService->addDocument('typo3', 'content', Argument::type(ContentDocument::class), 'tt_content_1')->shouldBeCalled();

        $fixture = [
            'uid' => 1,
            'pid' => 2,
            'header' => 'Some Header',
            'bodytext' => 'Some bodytext',
            'content_link' => 'Some content_link',
            'CType' => 'Some CType',
        ];

        $this->typo3Service->backendUtilityGetRecord('tt_content', 1)->willReturn($fixture)->shouldBeCalled();

        $this->elasticContentSaveHook->processDatamap_afterDatabaseOperations('', 'tt_content', 1, [], []);
    }
}

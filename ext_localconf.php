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

use TeamNeustaGmbH\M2T3\Elastictypo\Task\ContentReindex;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}


// Register with DataHandler:
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['tx_elastictypo'] = \TeamNeustaGmbH\M2T3\Elastictypo\Hook\ElasticContentSaveHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$_EXTKEY] = \TeamNeustaGmbH\M2T3\Elastictypo\Hook\ElasticContentSaveHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][$_EXTKEY] = \TeamNeustaGmbH\M2T3\Elastictypo\Hook\ElasticContentSaveHook::class;


// Add hotel lookup reindexer scheduler task
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][ContentReindex::class] = array(
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xml:contentReindex.name',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xml:contentReindex.description'
);
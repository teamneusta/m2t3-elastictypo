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

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}


call_user_func(static function (string $extensionKey) {
// Register with DataHandler:
    //$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['tx_elastictypo'] = \TeamNeustaGmbH\M2T3\Elastictypo\Hook\ElasticContentSaveHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$extensionKey] = \TeamNeustaGmbH\M2T3\Elastictypo\Hook\ElasticContentSaveHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][$extensionKey] = \TeamNeustaGmbH\M2T3\Elastictypo\Hook\ElasticContentSaveHook::class;
}, 'm2t3_elastictypo');

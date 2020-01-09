<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
/**
 * This file is part of the TeamNeustaGmbH/m2t3 package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/BSD-3-Clause  BSD-3-Clause License
 */

call_user_func(static function (string $extensionKey) {
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('scheduler')) {
        // Add hotel lookup reindexer scheduler task
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\TeamNeustaGmbH\M2T3\Elastictypo\Task\ContentReindex::class] = [
            'extension' => $extensionKey,
            'title' => "LLL:EXT:{$extensionKey}/Resources/Private/Language/locallang.xml:contentReindex.name",
            'description' => "LLL:EXT:{$extensionKey}/Resources/Private/Language/locallang.xml:contentReindex.description",
        ];
    }
}, 'm2t3_elastictypo');

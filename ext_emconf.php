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

/************************************************************************
 * Extension Manager/Repository config file for ext "skeleton".
 ************************************************************************/
$EM_CONF[$_EXTKEY] = [
    'title' => 'm2t3_elastictypo',
    'description' => 'elastic content',
    'category' => 'extension',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.99.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'TeamNeustaGmbH\\M2T3\\Elastictypo\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Benjamin Kluge',
    'author_email' => 'b.kluge@neusta.de',
    'author_company' => 'team neusta GmbH',
    'version' => '1.0.0',
];

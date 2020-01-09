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

namespace TeamNeustaGmbH\M2T3\Elastictypo\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * Class Typo3Service
 *
 * @codeCoverageIgnore
 * @package TeamNeustaGmbH\M2T3\Elastictypo\Service
 */
class Typo3Service
{
    /**
     * Gets record with uid = $uid from $table
     * You can set $field to a list of fields (default is '*')
     * Additional WHERE clauses can be added by $where (fx. ' AND blabla = 1')
     * Will automatically check if records has been deleted and if so, not return anything.
     * $table must be found in $GLOBALS['TCA']
     *
     * @param string $table Table name present in $GLOBALS['TCA']
     * @param int $uid UID of record
     * @param string $fields List of fields to select
     * @param string $where Additional WHERE clause, eg. " AND blablabla = 0
     * @param bool $useDeleteClause Use the deleteClause to check if a record is deleted (default TRUE)
     * @return array|NULL Returns the row if found, otherwise NULL
     */
    public function backendUtilityGetRecord(
        string $table,
        int $uid,
        string $fields = '*',
        string $where = '',
        bool $useDeleteClause = true
    ) {
        return BackendUtility::getRecord($table, $uid, $fields, $where, $useDeleteClause);
    }

    /**
     * Creates an instance of a class taking into account the class-extensions
     * API of TYPO3. USE THIS method instead of the PHP "new" keyword.
     * Eg. "$obj = new myclass;" should be "$obj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("myclass")" instead!
     *
     * You can also pass arguments for a constructor:
     * \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\myClass::class, $arg1, $arg2, ..., $argN)
     *
     * You may want to use \TYPO3\CMS\Extbase\Object\ObjectManager::get() if you
     * want TYPO3 to take care about injecting dependencies of the class to be
     * created. Therefore create an instance of ObjectManager via
     * GeneralUtility::makeInstance() first and call its get() method to get
     * the instance of a specific class.
     *
     * @param string $className name of the class to instantiate, must not be empty and not start with a backslash
     * @param mixed $constructorArguments Arguments for the constructor
     * @return object the created instance
     * @throws \InvalidArgumentException if $className is empty or starts with a backslash
     */
    public function generalUtilityMakeInstance($className, ...$constructorArguments)
    {
        return GeneralUtility::makeInstance($className, ...$constructorArguments);
    }

    /**
     * Initialize frontend rendering
     *
     * @param int $id
     * @param int $typeNum
     * @return void
     */
    public function initTSFE($id = 1, $typeNum = 0)
    {
        $this->eidUtilityInitTCA();
        if (!is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = new TimeTracker();
            $GLOBALS['TT']->start();
        }
        $GLOBALS['TSFE'] = $this->generalUtilityMakeInstance(
            TypoScriptFrontendController::class,
            $GLOBALS['TYPO3_CONF_VARS'],
            $id,
            $typeNum
        );
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getConfigArray();
    }

    /**
     * Makes TCA available inside eID
     *
     * @return void
     */
    public function eidUtilityInitTCA()
    {
        EidUtility::initTCA();
    }
}

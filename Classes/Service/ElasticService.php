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

namespace TeamNeustaGmbH\M2T3\Elastictypo\Service;

use Elastica\Client;
use Elastica\Document;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ElasticService
{
    /**
     * client
     *
     * @var Client
     */
    protected $client;

    /**
     * addDocument
     *
     * @param string $index
     * @param string $type
     * @param object $document
     * @param string $documentId
     * @return void
     */
    public function addDocument(string $index, string $type, $document, string $documentId)
    {
        $client = $this->getClient();
        $index = $client->getIndex($index);
        $type = $index->getType($type);
        $type->addDocument($this->convertModelToDocument($document, $documentId));
        $type->getIndex()->refresh();
    }

    /**
     * getClient
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return !empty($this->client) ? $this->client : new Client([
            'host' => $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['host'],
            'port' => $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['port'],
        ]);
    }

    /**
     * setClient
     *
     * @param \Elastica\Client $client
     * @return void
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * convertModelToDocument
     *
     * @param $documentModel
     * @param string $documentId
     *
     * @return \Elastica\Document|null
     */
    protected function convertModelToDocument($documentModel, string $documentId)
    {
        $getMethods = get_class_methods($documentModel);
        $document = [];
        foreach ($getMethods as $method) {
            if (substr($method, 0, 3) === 'get') {
                $elasticField = substr(GeneralUtility::camelCaseToLowerCaseUnderscored($method), 4);
                $document[$elasticField] = $documentModel->{$method}();
            }
        }

        if (!empty($document)) {
            return new Document($documentId, $document);
        }
    }

    /**
     * deleteDocument
     *
     * @param string $index
     * @param string $type
     * @param string $documentId
     * @return void
     */
    public function deleteDocument(string $index, string $type, string $documentId)
    {
        $client = $this->getClient();
        $index = $client->getIndex($index);
        $type = $index->getType($type);
        $doc = new Document($documentId);
        $type->deleteDocument($doc);
        $type->getIndex()->refresh();
    }

    /**
     * cleanIndex
     *
     * @param string $index
     * @return void
     */
    public function cleanIndex(string $index)
    {
        $client = $this->getClient();
        if ($client->getIndex($index)->exists()) {
            $client->getIndex($index)->delete();
        }
        $client->getIndex($index)->create();
    }
}
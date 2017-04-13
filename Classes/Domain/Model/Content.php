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

namespace TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Content
 *
 * @package TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model
 */
class Content extends AbstractEntity
{

    /**
     * uid
     *
     * @var integer
     */
    protected $uid;

    /**
     * pid
     *
     * @var integer
     */
    protected $pid;

    /**
     * header
     *
     * @var string
     */
    protected $header;

    /**
     * sorting
     *
     * @var string
     */
    protected $sorting;

    /**
     * contentType
     *
     * @var string
     */
    protected $contentType;
    /**
     * bodytext
     *
     * @var string
     */
    protected $bodytext;

    /**
     * @return string
     */
    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    /**
     * @param string $bodytext
     */
    public function setBodytext(string $bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid(int $uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader(string $header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getSorting(): string
    {
        return $this->sorting;
    }

    /**
     * @param string $sorting
     */
    public function setSorting(string $sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;
    }
}
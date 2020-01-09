<?php
declare(strict_types=1);

namespace TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model;

/**
 * This file is part of the TeamNeustaGmbH/m2t3 package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/BSD-3-Clause  BSD-3-Clause License
 */

/**
 * Class ContentDocument
 *
 * @package TeamNeustaGmbH\M2T3\Elastictypo\Domain\Model
 */
class ContentDocument
{
    /**
     * contentId
     *
     * @var integer
     */
    protected $contentId;
    /**
     * contentTitle
     *
     * @var string
     */
    protected $contentTitle;
    /**
     * content_headlines
     *
     * @var string
     */
    protected $content_headlines;
    /**
     * content_bodytexts
     *
     * @var string
     */
    protected $content_bodytexts;
    /**
     * content_link
     *
     * @var string
     */
    protected $content_link;
    /**
     * content_type
     *
     * @var string
     */
    protected $content_type;
    /**
     * page_id
     *
     * @var integer
     */
    protected $page_id;

    /**
     * @return string
     */
    public function getContentTitle(): string
    {
        return $this->contentTitle;
    }

    /**
     * @param string $contentTitle
     */
    public function setContentTitle(string $contentTitle)
    {
        $this->contentTitle = $contentTitle;
    }

    /**
     * @return string
     */
    public function getContentBodytexts(): string
    {
        return $this->content_bodytexts;
    }

    /**
     * @param string $content_bodytexts
     */
    public function setContentBodytexts(string $content_bodytexts)
    {
        $this->content_bodytexts = $content_bodytexts;
    }

    /**
     * @return string
     */
    public function getContentLink(): string
    {
        return $this->content_link;
    }

    /**
     * @param string $content_link
     */
    public function setContentLink(string $content_link)
    {
        $this->content_link = $content_link;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->content_type;
    }

    /**
     * @param string $content_type
     */
    public function setContentType(string $content_type)
    {
        $this->content_type = $content_type;
    }

    /**
     * @return int
     */
    public function getPageId(): int
    {
        return $this->page_id;
    }

    /**
     * @param int $page_id
     */
    public function setPageId(int $page_id)
    {
        $this->page_id = $page_id;
    }

    /**
     * @return int
     */
    public function getContentId(): int
    {
        return $this->contentId;
    }

    /**
     * @param int $contentId
     */
    public function setContentId(int $contentId)
    {
        $this->contentId = $contentId;
    }
}

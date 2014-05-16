<?php

namespace App\Model;

use DateTime;

/**
 * @Entity
 * @Table (name="items")
 */
class Item
{
    /**
     * @var int
     * @Id
     * @Column (type="bigint")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @var int
     * @Column (type="bigint")
     */
    protected $resourceId;

    /**
     * @var string
     * @Column (type="string")
     */
    protected $link;

    /**
     * @var DateTime
     * @Column (type="datetime")
     */
    protected $pubDate;

    /**
     * @var DateTime
     * @Column (type="datetime")
     */
    protected $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }

    public function setResourceId($id)
    {
        $this->resourceId = $id;

        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    public function getPubDate()
    {
        return $this->pubDate;
    }

    public function setPubDate($pubDate)
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
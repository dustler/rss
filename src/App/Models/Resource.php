<?php

namespace App\Model;

/**
 * @Entity
 * @Table (name="resources")
 */
class Resource
{
    /**
     * @var int
     * @Id
     * @Column (type="bigint")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column (type="string")
     */
    protected $link;

    /**
     * @var \DateTime
     * @Column (type="datetime")
     */
    protected $lastPubDate;

    public function getId()
    {
        return $this->id;
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

    public function getLastPubDate()
    {
        return $this->lastPubDate;
    }

    public function setLastPubDate($lastPubDate)
    {
        $this->lastPubDate = $lastPubDate;
    }
}
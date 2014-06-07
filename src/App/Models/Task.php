<?php

namespace App\Model;

/**
 * @Entity
 * @Table (name="tasks")
 */
class Task
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
    protected $taskId;

    /**
     * @var \DateTime
     * @Column (type="datetime")
     */
    protected $createdAt;

    /**
     * @var
     * @Column (type="string")
     */
    protected $url;

    public function getId()
    {
        return $this->id;
    }

    public function getTaskId()
    {
        return $this->taskId;
    }

    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;

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

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}

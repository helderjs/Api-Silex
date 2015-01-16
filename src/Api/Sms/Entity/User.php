<?php

namespace Api\Sms\Entity;


use DateTime;

/**
 * @Entity
 * @Table(name="user")
 */
class User
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string", unique=true)
     * @var string
     */
    protected $access_key;

    /**
     * @Column(type="datetime")
     * @var DateTime
     **/
    protected $created;

    /**
     * @Column(type="datetime")
     * @var DateTime
     **/
    protected $updated;

    public function __construct()
    {
        // constructor is never called by Doctrine
        $this->created = $this->updated = new DateTime("now");
    }

    /**
     * @PreUpdate
     */
    public function updated()
    {
        $this->updated = new DateTime("now");
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAccessKey()
    {
        return $this->access_key;
    }

    /**
     * @param string $key
     */
    public function setAccessKey($key)
    {
        $this->access_key = $key;
    }
}

<?php

namespace Api\Sms\Entity;

/**
 * @Entity
 * @Table(name="host", uniqueConstraints={@UniqueConstraint(name="host_unique", columns={"user_id", "ip", "name"})})
 **/
class Host
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     **/
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     **/
    protected $ip;

    /**
     * @Column(type="string")
     * @var string
     **/
    protected $name;

    /**
     * @ManyToOne(targetEntity="User")
     * @var User
     **/
    protected $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}

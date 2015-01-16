<?php

namespace Api\Sms\Entity;

/**
 * @ORM
 * @Entity
 * @Table(name="category", uniqueConstraints={@UniqueConstraint(name="category_idx", columns={"name", "user_id"})})
 **/
class Category
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
    protected $name;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="category", cascade={"persist"})
     * @JoinColumn(onDelete="CASCADE")
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

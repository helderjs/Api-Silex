<?php

namespace Api\Sms\Entity;

/**
 * @Entity
 * @Table(name="config", uniqueConstraints={@UniqueConstraint(name="config_idx", columns={"category_id", "gateway_id", "user_id"})})
 */
class Config
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Category", inversedBy="config", cascade={"persist", "remove"})
     * @JoinColumn(onDelete="CASCADE")
     * @var Category
     **/
    protected $category;

    /**
     * @ManyToOne(targetEntity="Gateway", inversedBy="config", cascade={"persist"})
     * @var Gateway
     **/
    protected $gateway;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="config", cascade={"persist", "remove"})
     * @JoinColumn(onDelete="CASCADE")
     * @var Gateway
     **/
    protected $user;

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
        $this->user = $category->getUser();
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Gateway $gateway
     */
    public function setGateway(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return Gateway
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

<?php

namespace Api\Sms\Entity;

use DateTime;

/**
 * @Entity
 * @Table(name="sms")
 * @HasLifecycleCallbacks
 **/
class Sms
{
    const SMS_STATUS_CREATED = 'CR';
    const SMS_STATUS_SENT = 'ST';
    const SMS_STATUS_SCHEDULE = 'SD';
    const SMS_STATUS_NO_CREDIT = 'NC';
    const SMS_STATUS_INVALID_NUMBER = 'IN';
    const SMS_STATUS_EXCEED_LIMIT = 'EL';
    const SMS_STATUS_BLACKLIST = 'BL';
    const SMS_STATUS_HTTP_ERROR = 'HE';
    const SMS_STATUS_ERROR = 'ER';

    const SMS_TYPE_IN = 'in';
    const SMS_TYPE_OUT = 'out';

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     **/
    protected $id;

    /**
     * @Column(type="string", length=2)
     * @var string
     **/
    protected $ddd;

    /**
     * @Column(type="string", length=9)
     * @var string
     **/
    protected $number;

    /**
     * @Column(type="string")
     * @var string
     **/
    protected $message;

    /**
     * @Column(type="string")
     * @var string
     **/
    protected $signature;

    /**
     * @Column(type="string")
     * @var string
     **/
    protected $status;

    /**
     * @Column(type="string")
     * @var string
     **/
    protected $type;

    /**
     * @ManyToOne(targetEntity="Category")
     * @var Category
     **/
    protected $category;

    /**
     * @ManyToOne(targetEntity="Gateway")
     * @var Gateway
     **/
    protected $gateway;

    /**
     * @ManyToOne(targetEntity="Sms")
     * @var Sms
     **/
    protected $sms;

    /**
     * @Column(type="datetime")
     * @var DateTime
     **/
    protected $schedule;

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
     * @PrePersist
     */
    public function beforePersist()
    {
        if (empty($this->schedule)) {
            $this->schedule = $this->created;
        }
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $ddd
     */
    public function setDdd($ddd)
    {
        $this->ddd = $ddd;
    }

    /**
     * @return string
     */
    public function getDdd()
    {
        return $this->ddd;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
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
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param Sms $sms
     */
    public function setSms(Sms $sms)
    {
        $this->sms = $sms;
    }

    /**
     * @return Sms
     */
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * @return DateTime
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @param DateTime $dateTime
     */
    public function setSchedule(DateTime $dateTime)
    {
        $this->schedule = $dateTime;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            array(
                'id' => $this->id,
                'ddd' => $this->ddd,
                'number' => $this->number,
                'message' => $this->message,
                'status' => $this->status,
                'type' => $this->type,
                'gateway' => is_object($this->gateway) ? $this->gateway->getName() : null,
                'category' => is_object($this->category) ? $this->category->getName() : null,
                'created' => $this->created->format('d/m/Y m:i:s'),
                'updated' => $this->updated->format('d/m/Y m:i:s'),
            )
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'ddd' => $this->ddd,
            'number' => $this->number,
            'message' => $this->message,
            'signature' => $this->signature,
            'status' => $this->status,
            'type' => $this->type,
            'gateway' => is_object($this->gateway) ? $this->gateway->getName() : null,
            'category' => is_object($this->category) ? $this->category->getName() : null,
            'schedule' => $this->schedule->format('d/m/Y m:i:s'),
            'created' => $this->created->format('d/m/Y m:i:s'),
            'updated' => $this->updated->format('d/m/Y m:i:s'),
        );
    }
}

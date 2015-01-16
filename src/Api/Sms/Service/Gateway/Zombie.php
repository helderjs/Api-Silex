<?php

namespace Api\Sms\Service\Gateway;

use Doctrine\Common\Collections\ArrayCollection;
use Api\Sms\Entity\Sms;

/**
 * Fake Service
 *
 * Class Zombie
 * @package Api\Sms\Service\Gateway
 */
class Zombie extends GatewayAbstract
{
    public function send(Sms $sms)
    {
        $returns = [0 => Sms::SMS_STATUS_ERROR, 1 => Sms::SMS_STATUS_SENT];

        return $returns[rand(0, 1)];
    }

    public function status(Sms $sms)
    {
        return $sms->getStatus();
    }

    public function responseSms(Sms $sms)
    {
        return new ArrayCollection();
    }
}

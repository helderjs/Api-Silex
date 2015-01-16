<?php
/**
 * Created by PhpStorm.
 * User: helder
 * Date: 07/07/14
 * Time: 12:41
 */

namespace Api\Sms\Service\Gateway;

use Api\Sms\Entity\Sms;

/**
 * Service to http://www.zenvia.com.br/
 *
 * Class Zenvia
 * @package Api\Sms\Service\Gateway
 */
class Zenvia extends GatewayAbstract
{
    /**
     * @inheritdoc
     */
    public function send(Sms $sms)
    {
        if (!$this->hasCredit()) {
            return Sms::SMS_STATUS_NO_CREDIT;
        }

        $url = 'http://www.zenvia360.com.br/GatewayIntegration/msgSms.do';
        $data = [
            'account' => $this->credentials['username'],
            'code' => $this->credentials['password'],
            'dispatch' => 'send',
            'from' => $sms->getSignature(),
            'to' => '55' . $sms->getDdd() . $sms->getNumber(),
            'msg' => $sms->getMessage(),
            'id' => $sms->getId(),
        ];

        try {
            $response = $this->client->post(
                $url,
                ['body' => $data]
            );
        } catch (\Exception $e) {
            return Sms::SMS_STATUS_HTTP_ERROR;
        }

        if ($response->getStatusCode() != 200) {
            return Sms::SMS_STATUS_HTTP_ERROR;
        }

        if ($response->getBody() == '000') {
            return Sms::SMS_STATUS_SENT;
        }

        if ($response->getBody() == '012') {
            return Sms::SMS_STATUS_EXCEED_LIMIT;
        }

        if ($response->getBody() == '013' || $response->getBody() == '014') {
            return Sms::SMS_STATUS_INVALID_NUMBER;
        }

        return Sms::SMS_STATUS_ERROR;
    }

    public function status(Sms $sms)
    {
        $url = 'http://www.zenvia360.com.br/GatewayIntegration/msgSms.do';
        $data = [
            'account' => $this->credentials['username'],
            'code' => $this->credentials['password'],
            'dispatch' => 'check',
            'id' => $sms->getId(),
        ];

        try {
            $response = $this->client->post($url, ['body' => $data]);
        } catch (\Exception $e) {
            throw new HttpException('500', 'Error while requesting data.');
        }

        if ($response->getBody() == '100') {
            return Sms::SMS_STATUS_SCHEDULE;
        }

        if ($response->getBody() == '110' || $response->getBody() == '120') {
            return Sms::SMS_STATUS_SENT;
        }

        if ($response->getBody() >= '140' && $response->getBody() <= '171') {
            return Sms::SMS_STATUS_ERROR;
        }

        return $sms->getStatus();
    }

    public function responseSms(Sms $sms)
    {
        $url = 'http://www.zenvia360.com.br/GatewayIntegration/msgSms.do';
        $data = [
            'account' => $this->credentials['username'],
            'code' => $this->credentials['password'],
            'dispatch' => 'listReceived',
        ];

        try {
            $response = $this->client->post($url, ['body' => $data]);
        } catch (\Exception $e) {
            throw new HttpException('500', 'Error while requesting data.');
        }

        if ($response->getStatusCode() != 200) {
            throw new HttpException('500', 'Error while requesting data.');
        }

        return [];
    }
}

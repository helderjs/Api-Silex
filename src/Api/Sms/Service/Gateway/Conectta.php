<?php

namespace Api\Sms\Service\Gateway;

use Doctrine\Common\Collections\ArrayCollection;
use Api\Sms\Entity\Sms;
use Symfony\Component\HttpKernel\Exception\HttpException;
use SimpleXMLElement;
use DateInterval;

/**
 * Service to http://www.conectta.com.br/
 *
 * Class Conectta
 * @package Api\Sms\Service\Gateway
 */
class Conectta extends GatewayAbstract
{
    /**
     * @inheritdoc
     */
    public function hasCredit($amount = 0)
    {
        $url = "http://conecttasms.com.br/http/getSaldo.ashx";
        $data = array(
            'Login' => $this->credentials['username'],
            'Senha' => $this->credentials['password'],
        );

        $response = $this->httpRequest($url, $data);

        $credit = $response->getBody()->__toString();

        if ($credit <= $amount) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function send(Sms $sms)
    {
        if (!$this->hasCredit()) {
            return Sms::SMS_STATUS_NO_CREDIT;
        }

        $url = 'http://sms.conectta.com.br/http/CampanhasList.ashx';
        $data = [
            'Remetente' => $sms->getSignature(),
            'Celular' => "55" . $sms->getDdd() . $sms->getNumber(),
            'Mensagem' => $sms->getMessage() . " " . $sms->getSignature(),
            'Titulo' => '',
            'Filial' => '',
            'DataEnvio' => $sms->getUpdated()->format('d/m/Y'),
            'Hora' => $sms->getUpdated()->format('h:i'),
            'InfosRetorno' => $sms->getId(),
        ];

        try {
            $response = $this->client->post(
                $url,
                ['headers' => ['Content-Type' => "application/xml; charset=UTF-8"], 'body' => $this->sendXml($data)]
            );
        } catch (\Exception $e) {
            return Sms::SMS_STATUS_HTTP_ERROR;
        }

        if ($response->getStatusCode() != 200) {
            return Sms::SMS_STATUS_HTTP_ERROR;
        }

        if (strstr($response->getBody(), 'SUCESSO')) {
            return Sms::SMS_STATUS_SCHEDULE;
        }

        return Sms::SMS_STATUS_ERROR;
    }

    public function status(Sms $sms)
    {
        $url = 'http://conecttasms.com.br/http/CampanhasEnviadas.ashx?';
        $data = [
            'Login' => $this->credentials['username'],
            'Senha' => $this->credentials['password'],
            'DataIn' => $sms->getCreated()->format('d/m/Y'),
            'DataOut' => $sms->getUpdated()->format('d/m/Y'),
        ];

        try {
            $response = $this->client->get(
                $url,
                ['query' => $data]
            );
        } catch (\Exception $e) {
            throw new HttpException('500', 'Error while requesting data.');
        }

        $xml = new SimpleXMLElement($response->getBody());
        foreach ($xml->Campanhas as $campaign) {
            $reference = $campaign['IdReferencia'];
            $smsStatus = (string)$campaign['Report'];

            if ($reference == $sms->getId()) {
                if (strstr($smsStatus, 'ENVIADA') or strstr($smsStatus, 'ENTREGUE')) {
                    return Sms::SMS_STATUS_SENT;
                } elseif (strstr($smsStatus, 'AGENDADA') or strstr($smsStatus, 'ENVIANDO')) {
                    return Sms::SMS_STATUS_SCHEDULE;
                } else {
                    return Sms::SMS_STATUS_ERROR;
                }
            }
        }

        return $sms->getStatus();
    }

    public function responseSms(Sms $sms)
    {
        $url = 'http://sms.conectta.com.br/Http/Retornos.asmx';
        $endDate = clone $sms->getCreated();
        $endDate->add(new DateInterval('P7D'));
        $data = [
            'beginDate' => $sms->getCreated()->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
        ];

        try {
            $response = $this->client->post(
                $url,
                [
                    'headers' => [
                        'Content-Type' => "text/xml; charset=UTF-8",
                        'SOAPAction' => 'http://tempuri.org/GetRetornoByData'
                    ],
                    'body' => $this->responseXml($data)
                ]
            );
        } catch (\Exception $e) {
            throw new HttpException('500', 'Error while requesting data.');
        }

        if ($response->getStatusCode() != 200) {
            throw new HttpException('500', 'Error while requesting data.');
        }

        $xml = new SimpleXMLElement($response->getBody());
        $soapBody = $xml->xpath('//soap:Body');
        $body = new SimpleXMLElement($soapBody[0]->GetRetornoByDataResponse->GetRetornoByDataResult);

        $responses = new ArrayCollection();
        foreach ($body->RetornoCliente as $responseSms) {
            if ($responseSms['InfosRetorno'] == $sms->getId()) {
                $newResponse = new Sms();
                $newResponse->setDdd(substr($responseSms['Celular'], 2, 2));
                $newResponse->setNumber(substr($responseSms['Celular'], 4));
                $newResponse->setType(Sms::SMS_TYPE_IN);
                $newResponse->setStatus(Sms::SMS_STATUS_SENT);
                $newResponse->setMessage((string)$responseSms['MensagemCliente']);
                $newResponse->setSignature((string)$responseSms['Celular']);
                $newResponse->setCategory($sms->getCategory());
                $newResponse->setGateway($sms->getGateway());
                $newResponse->setSms($sms);

                $responses->add($newResponse);
            }
        }

        return $responses;
    }

    public function sendXml(array $data)
    {
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<ConecttaSMS>
    <Autenticacao>
        <Login>{$this->credentials['username']}</Login>
        <Senha>{$this->credentials['password']}</Senha>
    </Autenticacao>
    <Campanha>
        <Remetente>{$data['Remetente']}</Remetente>
        <Celular>{$data['Celular']}</Celular>
        <Mensagem>{$data['Mensagem']}</Mensagem>
        <Filial></Filial>
        <DataEnvio>{$data['DataEnvio']}</DataEnvio>
        <InfosRetorno>{$data['InfosRetorno']}</InfosRetorno>
        <Titulo></Titulo>
        <Hora>{$data['Hora']}</Hora>
    </Campanha>
</ConecttaSMS>
XML;
        return $xml;
    }

    public function responseXml(array $data)
    {
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetRetornoByData xmlns="http://tempuri.org/">
      <login>{$this->credentials['username']}</login>
      <senha>{$this->credentials['password']}</senha>
      <dataInicial>{$data['beginDate']}</dataInicial>
      <dataFinal>{$data['endDate']}</dataFinal>
    </GetRetornoByData>
  </soap:Body>
</soap:Envelope>
XML;
        return $xml;
    }
}

<?php

namespace Api\Sms\Service\Gateway;


use GuzzleHttp\Client;

abstract class GatewayAbstract implements GatewayInterface
{
    const REQUEST_GET = "GET";
    const REQUEST_POST = "POST";

    protected $credentials = array();

    protected $client;

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
        $this->client = new Client();
    }

    /**
     * @inheritdoc
     */
    public function hasCredit($amount = 0)
    {
        return true;
    }

    public function httpRequest($url, array $data, $type = self::REQUEST_GET, $headers = array())
    {
        $request = $this->client->createRequest($type, $url);

        if (!empty($headers)) {
            foreach ($headers as $header => $value) {
                $request->setHeader($header, $value);
            }
        } else {
            $request->setHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        }

        if ($type == self::REQUEST_GET) {
            $params = $request->getQuery();

            foreach ($data as $key => $value) {
                $params->set($key, $value);
            }
        }

        if ($type == self::REQUEST_POST) {
            $params = $request->getBody();

            foreach ($data as $key => $value) {
                $params->setField($key, $value);
            }
        }

        $response = $this->client->send($request);

        return $response;
    }
}

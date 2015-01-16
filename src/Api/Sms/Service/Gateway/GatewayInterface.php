<?php

namespace Api\Sms\Service\Gateway;

use Api\Sms\Entity\Sms;

interface GatewayInterface
{
    /**
     * Verifica se existe crédito suficiente para enviar sms
     *
     * @param int $amount Valor limite de créditos
     * @return bool
     */
    public function hasCredit($amount = 0);

    /**
     * Envia um mensagem sms através do gateway
     *
     * @param Sms $sms Informações da mensagem
     * @return bool
     */
    public function send(Sms $sms);

    /**
     * Recupera do gateway, status de sms enviado pelo serviço
     *
     * @param Sms $sms
     * @return array Array com status do sms
     */
    public function status(Sms $sms);

    /**
     * Recupera do gateway, respostas de sms enviado pelo serviço
     *
     * @param Sms $sms
     * @return array Array de respostas encontradas
     */
    public function responseSms(Sms $sms);
}

<?php
namespace Coinbar\CoinbarPay\Api;

interface PaymentNotifyInterface
{
    /**
     * @return string
     */
    public function execute(string $responseToken, string $serviceClientId, int $timestamp);
}

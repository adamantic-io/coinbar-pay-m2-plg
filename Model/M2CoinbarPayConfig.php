<?php
/*
 * Copyright (c) Coinbar Spa 2023.
 * This file is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the software.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Coinbar\CoinbarPay\Model;

use CoinbarPay\Sdk\CoinbarPaymentGateway;
use CoinbarPay\Sdk\CoinbarPaymentGatewayConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;

class M2CoinbarPayConfig extends CoinbarPaymentGatewayConfig {

    protected $_keyMapping = array();

    protected $_scopeConfig;
    protected $_urlBuilder;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder,
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_urlBuilder = $urlBuilder;
    }


    function loadConfig() {
        $prefix = "payment/coinbarpay/";

        $this->_keyMapping = array(
            CoinbarPaymentGatewayConfig::CBPAY_GW_URL                => $this->_scopeConfig->getValue($prefix . 'gateway_url'),
            CoinbarPaymentGatewayConfig::CBPAY_SERVICE_CLIENT_ID     => $this->_scopeConfig->getValue($prefix . 'service_client_id'),
            CoinbarPaymentGatewayConfig::CBPAY_TOKEN_API_KEY         => $this->_scopeConfig->getValue($prefix . 'token_api_key'),
            CoinbarPaymentGatewayConfig::CBPAY_TOKEN_SECRET_KEY      => $this->_scopeConfig->getValue($prefix . 'token_secret_key')
        );

        $this->_keyMapping[CoinbarPaymentGatewayConfig::CBPAY_BACKEND_CALLBACK_URL] = $this->_urlBuilder->getUrl('coinbarpay/payment/notify');
        $this->_keyMapping[CoinbarPaymentGatewayConfig::CBPAY_FRONTEND_CALLBACK_URL] = $this->_urlBuilder->getUrl('coinbarpay/payment/redirect');
    }

    function get($key): string {
        return $this->_keyMapping[$key];
    }

    function dump() {
        var_dump($this->_keyMapping);
    }
}
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

use Adamantic\CryptoPayments\Currency;
use Adamantic\CryptoPayments\PaymentGatewayConfig;
use Adamantic\CryptoPayments\PaymentRequest;
use Adamantic\CryptoPayments\PaymentRequestSimpleItem;
use Adamantic\CryptoPayments\PaymentStatus;
use Brick\Math\BigDecimal;
use CoinbarPay\Sdk\CoinbarCypher;
use CoinbarPay\Sdk\CoinbarPaymentGateway;
use CoinbarPay\Sdk\CoinbarPaymentGatewayConfig;
use CoinbarPay\Sdk\CoinbarPaymentStatusMapper;

use \Magento\Framework\Model\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;

/**
 * Pay In Store payment method model
 */
class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'coinbarpay';
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = false;

    protected $_m2CoinbarPayConfig;
    protected $_checkoutSession;

    public function __construct(
        M2CoinbarPayConfig $m2CoinbarPayConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        $this->_m2CoinbarPayConfig = $m2CoinbarPayConfig;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $resource, $resourceCollection, $data, $directory);
    }

    public function getOrderPlaceRedirectUrl()
    {
        $gateway = $this->_m2CoinbarPayConfig->createGateway();
        $req = $this->createPaymentRequest();
        $rqu = $gateway->requestPayment($req);
        $fru = $rqu->getFrontendRedirectUrl();
        $this->_checkoutSession->setData('coinbarpay_redirect_url', $fru);
        return $fru;
    }

    protected function createPaymentRequest(): PaymentRequest {
        $preq = PaymentRequest::createNew();
        $this->_checkoutSession->setLoadInactive(true);
        $quote = $this->_checkoutSession->getQuote();
        $customer = $quote->getCustomer();
        $items = $quote->getItemsCollection();
        $customerId = $customer ? $customer->getId() ?? "-1" : "-1";
//var_dump($order);
//var_dump($items);
$this->_logger->info('Quote ID', [$quote->getId()]);
$this->_logger->info('Customer ID', [$customerId]);
$this->_logger->info('Customer Email', [$quote->getCustomerEmail()]);
        return $preq
            ->setUuid($quote->getId())
            ->setUserId($customerId)
            ->setUserEmail($quote->getCustomerEmail())
            ->setCurrency(new Currency('EUR', 2))
            ->addItem((new PaymentRequestSimpleItem())
                ->setId($quote->getId())
                ->setUnits(1)
                ->setType('ecommerce')
                ->setDescription('Order #' . $quote->getId())
                ->setAmount(BigDecimal::of($quote->getGrandTotal())));
    }

}
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
namespace Coinbar\CoinbarPay\Controller\Payment;

use Coinbar\CoinbarPay\Model\M2CoinbarPayConfig;
use Coinbar\CoinbarPay\Api\PaymentNotifyInterface;
use CoinbarPay\Sdk\CoinbarCypher;
use Magento\Framework\Exception\InputException;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;

use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Repository as PaymentRepository;

class Notify implements PaymentNotifyInterface
{
    protected $_m2CoinbarPayConfig;
    protected $_paymentRepository;
    protected $_orderRepository;

    public function __construct(
        M2CoinbarPayConfig       $m2CoinbarPayConfig,
        OrderRepositoryInterface $orderRepository,
        PaymentRepository        $paymentRepository
    ) {
        $this->_m2CoinbarPayConfig = $m2CoinbarPayConfig;
        $this->_orderRepository = $orderRepository;
        $this->_paymentRepository = $paymentRepository;
    }

    public function execute(string $responseToken, string $serviceClientId, int $timestamp)
    {
        $this->_m2CoinbarPayConfig->loadConfig();
        //$this->_m2CoinbarPayConfig->dump();

        if ($serviceClientId != $this->_m2CoinbarPayConfig->get(M2CoinbarPayConfig::CBPAY_SERVICE_CLIENT_ID)) {
            throw new \Magento\Framework\Exception\InputException(__("Invalid service client ID"));
        }

        $cipher = new CoinbarCypher($this->_m2CoinbarPayConfig);
        $decoded = $cipher->decode($responseToken);
        if (!$decoded) {
            throw new \Magento\Framework\Exception\InputException(__("Invalid response token, could not decode"));
        }

        $res = json_decode($decoded);
        if (!$res->payment_request_id_client) {
            throw new InputException(__("Field not provided: %1", 'payment_request_id_client'));
        }

        $order = $this->_orderRepository->get($res->payment_request_id_client);

        if (strcasecmp($res->result, 'success') == 0) {
          $this->addPaymentInfo($order, $res);
        }
        $this->_orderRepository->save($order);

        //var_dump($order);
        return [
          'result' => 'success',
          'message' => __('Order updated')->render()
        ];
    }

    private function addPaymentInfo(Order $order, $resultObj) {
      $payment = $order->getPayment();
      if ($payment->getLastTransId() != null && strcasecmp($payment->getLastTransId(), $resultObj->payment_id_coinbar) == 0) {
        throw new InputException(__("Payment ID already registered: %1", $resultObj->payment_id_coinbar));
      }
      $payment->setMethod('coinbarpay')
              ->setAmountOrdered($order->getGrandTotal())
              ->setAmountPaid($resultObj->payment_detail->total_price)
              ->setLastTransId($resultObj->payment_id_coinbar);
      $order->addStatusToHistory(Order::STATE_PROCESSING, __("Payment received: %1", $resultObj->payment_id_coinbar));
      $order->setTotalPaid($order->getTotalPaid() + $resultObj->payment_detail->total_price);
      $this->_paymentRepository->save($payment);
    }
}

/*
SUCCESSFUL EXAMPLE:
{
  "result": "success",
  "payment_id_coinbar": "499f5adb-5911-4d1f-8642-2c53c24075d9",
  "payment_request_id_client": "1",
  "service_client_id": "2cb63353-2541-4c4e-90d4-7bf1d4b42c5c",
  "customer": {
    "email": "eisenach@gmail.com"
  },
  "products": [
    {
      "_id": "647e1e13d4baa549c33bbafe",
      "product_name": "Order #1",
      "product_price": 0.05,
      "product_amount": 1,
      "product_id": "1",
      "product_type": "ecommerce"
    }
  ],
  "input_coin": "EUR",
  "status": "SUCCESS",
  "payment_detail": {
    "transaction_id": "DFE92F4E-736B-420A-BCBE-B53485DD1546",
    "timestamp": "2023-06-05T17:41:31.864Z",
    "total_price": 0.05
  },
  "creation_time": "2023-06-05T17:40:36.184Z",
  "update_time": "2023-06-05T17:41:24.123Z"
}

*/
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
namespace Coinbar\CoinbarPay\Block;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;

class Redirect extends Template
{
    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var FilterManager
     */
    protected $_filterManager;

    /**
     * @param Context $context
     * @param Order $order
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        FilterManager $filterManager,
        Order $order
    ) {
        parent::__construct($context);
        $this->_request = $request;
        $this->_filterManager = $filterManager;
        $this->_order = $order;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->_order->getIncrementId();
    }

    /**
     * @return string
     */
    public function getPaymentStatus() {
        return $this->escapeHtml($this->_request->getParam('status'));
    }

    /**
     * @return string
     */
    public function getPaymentId() {
        return $this->escapeHtml($this->_request->getParam('payment_id'));
    }


}

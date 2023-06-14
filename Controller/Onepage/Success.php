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
namespace Coinbar\CoinbarPay\Controller\Onepage;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;

class Success extends \Magento\Checkout\Controller\Onepage\Success
{

    protected $checkoutSession;
    protected $order;
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AccountManagementInterface  $accountManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order $order,
        \Psr\Log\LoggerInterface $logger,
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory
        );
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        $this->logger = $logger;
        $this->logger->info("Onepage Success Constructing");
    }

    public function execute()
    {
        $this->logger->info("Onepage Success Executing");
        $orderId = $this->checkoutSession->getLastOrderId();
        $order = $this->order->load($orderId);

        if ($order->getPayment()->getMethod() == 'coinbarpay') {
            $redirectUrl = $this->checkoutSession->getData('coinbarpay_redirect_url');
            $this->logger->info("Onepage Success redirecting " . $redirectUrl);
            $this->_redirect($redirectUrl);
        }

        return parent::execute();
    }
}

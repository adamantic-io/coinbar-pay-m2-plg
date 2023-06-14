/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Coinbar_CoinbarPay/js/action/set-payment-method-action'
    ],
    function (Component, setPaymentMethodAction) {
        'use strict';

        return Component.extend({
            defaults: {
//                redirectAfterPlaceOrder: false,
                template: 'Coinbar_CoinbarPay/payment/coinbarpay'
            },
//            afterPlaceOrder: function () {
//                setPaymentMethodAction(this.messageContainer);
//                return false;
//            }
        });
    }
);
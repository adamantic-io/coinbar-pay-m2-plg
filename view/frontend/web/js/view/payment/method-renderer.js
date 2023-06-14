define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'coinbarpay',
                component: 'Coinbar_CoinbarPay/js/view/payment/method-renderer/coinbarpay'
            }
        );
        return Component.extend({});
    }
);
/*
* Copyright © 2020 GBPrimePay Checkout.
*/
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        $,
        Component,
        rendererList
    ) {
        'use strict';

        var methods = [
            {
                type: 'gbprimepay_checkout',
                component: 'GBPrimePay_Checkout/js/view/payment/method-renderer/gbprimepay-checkout'
            }
        ];

        $.each(methods, function (k, method) {
            rendererList.push(method);
        });

        /** Add view logic here if needed */
        return Component.extend({});
    }
);

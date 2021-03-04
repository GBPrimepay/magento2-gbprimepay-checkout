<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Helper;

class Constant
{
    const TABLE_PREFIX = 'gbprimepay_checkout_';

    const PLATFORM = 'magento';
    const MODE = 'payment';
    const STATUS = 'draft';

    const URL_CHECKOUT_TEST = 'https://checkout.globalprimepay.com';
    const URL_CHECKOUT_LIVE = 'https://checkout.gbprimepay.com';

    const URL_MERCHANT_TEST = 'https://api.globalprimepay.com/getmerchantinfo';
    const URL_MERCHANT_LIVE = 'https://api.gbprimepay.com/getmerchantinfo';

    const URL_CHECKPUBLICKEY_TEST = 'https://api.globalprimepay.com/checkPublicKey';
    const URL_CHECKPUBLICKEY_LIVE = 'https://api.gbprimepay.com/checkPublicKey';

    const URL_CHECKPRIVATEKEY_TEST = 'https://api.globalprimepay.com/checkPrivateKey';
    const URL_CHECKPRIVATEKEY_LIVE = 'https://api.gbprimepay.com/checkPrivateKey';

    const URL_CHECKCUSTOMERKEY_TEST = 'https://api.globalprimepay.com/checkCustomerKey';
    const URL_CHECKCUSTOMERKEY_LIVE = 'https://api.gbprimepay.com/checkCustomerKey';
}

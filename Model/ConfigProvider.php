<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Model;

use GBPrimePay\Checkout\Model\GBPrimePayCheckout;
use GBPrimePay\Checkout\Model\GBPrimePayInstallment;
use GBPrimePay\Checkout\Model\GBPrimePayQrcode;
use GBPrimePay\Checkout\Model\GBPrimePayQrcredit;
use GBPrimePay\Checkout\Model\GBPrimePayQrwechat;
use GBPrimePay\Checkout\Model\GBPrimePayLinepay;
use GBPrimePay\Checkout\Model\GBPrimePayTruewallet;
use GBPrimePay\Checkout\Model\GBPrimePayMbanking;
use GBPrimePay\Checkout\Model\GBPrimePayBarcode;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;

class ConfigProvider implements ConfigProviderInterface
{
    protected $methodCodes = [
        GBPrimePayCheckout::CODE,
        GBPrimePayDirect::CODE,
        GBPrimePayInstallment::CODE,
        GBPrimePayQrcode::CODE,
        GBPrimePayQrcredit::CODE,
        GBPrimePayQrwechat::CODE,
        GBPrimePayLinepay::CODE,
        GBPrimePayTruewallet::CODE,
        GBPrimePayMbanking::CODE,
        GBPrimePayBarcode::CODE
    ];

    protected $methods = [];

    protected $escaper;

    protected $_configHelper;

    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $session,
        \GBPrimePay\Checkout\Helper\ConfigHelper $configHelper
    ) {
        $this->escaper = $escaper;
        $this->_configHelper = $configHelper;
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $session;
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    public function getConfig()
    {
        return [];
    }
}

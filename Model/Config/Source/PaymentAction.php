<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PaymentAction implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'authorize',
                'label' => __('Authorize Only'),
            ],
            [
                'value' => 'authorize_capture',
                'label' => __('Authorize and Capture')
            ]
        ];
    }
}

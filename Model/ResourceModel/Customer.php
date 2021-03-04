<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Model\ResourceModel;

use GBPrimePay\Checkout\Helper\Constant as Constant;

class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $_prefix = Constant::TABLE_PREFIX;
        $this->_init($_prefix . 'customer', 'id');
    }
}

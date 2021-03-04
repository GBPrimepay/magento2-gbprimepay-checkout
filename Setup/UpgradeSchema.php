<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Setup;


use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;
use GBPrimePay\Checkout\Helper\Constant as Constant;

/**
 * DB setup script for TokenBase
 */
class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     * DB setup code
     *
     * @param \Magento\Framework\Setup\UpgradeSchemaInterface $upgrade
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {


        $_prefix = Constant::TABLE_PREFIX;
        $version = $context->getVersion();
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.9.0') < 0) {}
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
        }
    }
}

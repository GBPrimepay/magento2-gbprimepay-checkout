<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Block\Customer;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\View\Element\Template;

class Card extends Template
{
    public $_configHelper;
    protected $cardFactory;
    protected $customerSession;


    public function __construct(
        Context $context,
        \GBPrimePay\Checkout\Helper\ConfigHelper $configHelper,
        \GBPrimePay\Checkout\Model\CardFactory $cardFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data
    ) {

        $this->customerSession = $customerSession;
        $this->cardFactory = $cardFactory;
        $this->_configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    public function getDataCard()
    {
        $customer_id = $this->customerSession->getCustomerId();
        $testModel = $this->cardFactory->create()
            ->getCollection()
            ->addFieldToFilter("magento_customer_id", $customer_id)
            ->getData();
        $this->checkFlag = count($testModel);

        return $testModel;
    }

    public function getConfigData()
    {
        return $this->_configHelper;
    }
}

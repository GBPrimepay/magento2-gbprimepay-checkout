<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\AbstractElement;
use \Magento\Backend\Block\Template;
use GBPrimePay\Checkout\Helper\Constant;

class Gbpbrand extends FormField
{

    protected $_config;

    protected $storeManager;

    public function __construct(
        Template\Context $context,
        \GBPrimePay\Checkout\Helper\ConfigHelper $configHelper,
        array $data = []
    ) {
        $this->_config = $configHelper;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * Unset some non-related element parameters
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {

          $html = '<img style="padding:0px 0px 0px 0px" src=' .
              $this->_config->getImageURLs('logo') .
              ' alt="gbprimepay.com">
              <br>
              <label style="padding:0px 0px 10px 0px">GBPrimePay Checkout</label>';

        return $html;
    }

}

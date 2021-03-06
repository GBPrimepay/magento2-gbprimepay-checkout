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

class ActiveCallbackQrcode extends FormField
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
     * Set template to itself
     *
     * @return \GBPrimePay\Checkout\Block\Adminhtml\System\Config\ActiveCallbackQrcode
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/active.phtml');
        }

        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $check = $this->checkEnable();

        if ($check) {
            $buttonLabel = "GBPrimePay QR Code : Active";
            $class = "callback-actived";
        } else {
            $buttonLabel = "GBPrimePay QR Code : Inactive";
            $class = "";
        }

        $this->addData(
            [
                'add_class' => __($class),
                'button_label' => __($buttonLabel),
                'html_id' => "active_callback_qrcode_transaction_button",
                'ajax_url' => $this->_urlBuilder->getUrl('gbprimepay/system_config/activeCallbackQrcode'),
            ]
        );

        return $this->_toHtml();
    }

    public function checkEnable()
    {
        try {
          if ($this->_config->getActiveQrcode()) {
            $callbackUrl = $this->storeManager->getStore()->getBaseUrl() . 'gbprimepay/checkout/callbackqrcode';

            if ($this->_config->getEnvironment() === 'prelive') {
                $url = Constant::URL_CHECKPUBLICKEY_TEST;
            } else {
                $url = Constant::URL_CHECKPUBLICKEY_LIVE;
            }
            $callback = $this->_config->sendPublicCurl("$url", [], 'GET');



                if (!empty($callback['merchantId']) && !empty($callback['initialShop']) && !empty($callback['merchantName'])) {
                        if ($this->_config->getEnvironment() === 'prelive') {
                            $url = Constant::URL_CHECKPRIVATEKEY_TEST;
                        } else {
                            $url = Constant::URL_CHECKPRIVATEKEY_LIVE;
                        }
                        $callback = $this->_config->sendPrivateCurl("$url", [], 'GET');
                            if (!empty($callback['merchantId']) && !empty($callback['initialShop']) && !empty($callback['merchantName'])) {
                              if ($this->_config->getEnvironment() === 'prelive') {
                                  $url = Constant::URL_CHECKCUSTOMERKEY_TEST;
                              } else {
                                  $url = Constant::URL_CHECKCUSTOMERKEY_LIVE;
                              }
                              $callback = $this->_config->sendTokenCurl("$url", [], 'POST');
                                  if (!empty($callback['merchantId']) && !empty($callback['initialShop']) && !empty($callback['merchantName'])) {
                                          return true;
                                  }
                            }
                }


        }
      } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}

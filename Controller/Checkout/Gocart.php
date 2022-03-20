<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Controller\Checkout;

use Magento\Framework\Registry;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Csp\Api\CspAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use GBPrimePay\Checkout\Helper\Constant;
use GBPrimePay\Checkout\Controller\Checkout\CsrfValidator;

class Gocart extends \GBPrimePay\Checkout\Controller\Checkout
{
  /**
   * Dispatch request
   *
   * @return \Magento\Framework\Controller\ResultInterface\ResponseInterface
   * @throws \Magento\Framework\Exception\NotFoundException
   */
  public function execute()
  {
        $_orderId = $this->checkoutSession->getData('last_order_id');
        if ($_orderId) {
          $this->checkoutSession->restoreQuote();
          $this->_messageManager->addWarning(
              __('GBPrimePay Checkout Failed!. Please try again.')
          );
          $argument = ['_current' => true];
          $resultRedirect = $this->RedirectFactory->create();
          $resultRedirect->setPath('checkout/cart',$argument);    
          return $resultRedirect;
        }else{
          $resultRedirect = $this->RedirectFactory->create();
          $resultRedirect->setPath('/');
          return $resultRedirect;
        }
  }

  public function modifyCsp(array $appliedPolicies): array
  {
      $appliedPolicies[] = new \Magento\Csp\Model\Policy\FetchPolicy(
          'form-action',
          false,
          ['https://api.gbprimepay.com/web/ktc_gateway/success','https://api.gbprimepay.com/web/ktc_gateway/cancel','https://api.gbprimepay.com/web/ktc_gateway/fail','https://api.gbprimepay.com/web/bbl_gateway/receive/goback/success','https://api.gbprimepay.com/web/bbl_gateway/receive/goback/fail','https://api.gbprimepay.com/web/bbl_gateway/receive/goback/cancel','https://api.gbprimepay.com/web/thanachat_gateway/receive/go_back','https://api.gbprimepay.com/web/scb_gateway/receive/realtime','https://api.gbprimepay.com/web/gateway/receive/goback','https://api.gbprimepay.com/gbp/gateway/receive/goback','https://api.globalprimepay.com/web/thanachat_gateway/receive/go_back'],
          ['https']
      );

      return $appliedPolicies;
  }
}
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

class Cancel extends \GBPrimePay\Checkout\Controller\Checkout
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
        $orderId = $this->getIncrementIdByOrderId($_orderId);
        $order = $this->getQuoteByOrderId($orderId); 
        $_getCustomerId = $order->getCustomerId();
        $payment = $order->getPayment();
        $transaction_form_additional = $payment->getAdditionalInformation("transaction_form");
            $isLogin = $this->customerSession->isLoggedIn();
            if ($isLogin) {
            }else{
              if(!empty($_getCustomerId)){
                $transaction_form = $this->reloadCustomerId($payment, $_getCustomerId, $transaction_form_additional);
              }
            }
                  if ($orderId) {
                  
                    if(empty($transaction_form)){
                      $transaction_form = $transaction_form_additional;
                    }
                    $order_note = "Payment Canceled, Order canceled by customer";
                    $this->failureOrder($orderId, "canceled", $order_note);
                  }        
      
    return $this->PageFactory->create();
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
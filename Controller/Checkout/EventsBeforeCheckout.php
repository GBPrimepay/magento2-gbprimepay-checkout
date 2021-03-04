<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Controller\Checkout;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Action;


class EventsBeforeCheckout extends \GBPrimePay\Checkout\Controller\Checkout
  {

      /**
       * Dispatch request
       *
       * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
       * @throws \Magento\Framework\Exception\NotFoundException
       */
      public function execute()
      {
          try {
              $this->_eventManager->dispatch('gbprimepay_before_checkout');
              $selected = $this->_config->setGBPMethod('selected_checkout');
              
              $_transaction_id = $this->_config->getGBPTransactionID();
              $_transaction_key = $this->_config->getGBPTransactionKEY();
              $_transaction_info = $this->_config->getGBPTransactionINFO();        


              

              $generateitem = $this->_config->getGBPTransactionITEM();
            //   $_postload = serialize($generateitem);
              $_postload = '';
              $_form_key = $this->_config->getGBPFormKEY();
              $isLogin = $this->customerSession->isLoggedIn();
              if ($isLogin) {
              $currentdate = date('Y-m-d H:i');
              $purchase = array(
                  "id_customer" => $this->customerSession->getCustomerId(),
                  "quoteid" => $_transaction_id,
                  "postload" => $_postload,
                  "method" => 'gbprimepay_checkout',
                  "status" => 'active'
              );
              $save_purchase = $this->gbprimepayCheckout->_purchaseData($purchase);
              }

              return $this->jsonFactory->create()->setData([
                  'success' => true,
                  'transaction_id' => $_transaction_id,
                  'transaction_key' => $_transaction_key,
                  'transaction_info' => $_transaction_info,
                  'form_key' => $_form_key,
                  'selected' => 'selected_checkout'
              ]);


          } catch (\Exception $exception) {
              return $this->jsonFactory->create()->setData([
                  'success' => false,
                  'error' => true,
                  'message' => $exception->getMessage()
              ]);
          }
      }
  }

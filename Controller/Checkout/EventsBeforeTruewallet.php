<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Controller\Checkout;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Action;


class EventsBeforeTruewallet extends \GBPrimePay\Checkout\Controller\Checkout
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
              $this->_eventManager->dispatch('gbprimepay_before_truewallet');
              $selected = $this->_config->setGBPMethod('selected_truewallet');
              
              $_transaction_id = $this->_config->getGBPTransactionID();
              $_transaction_key = $this->_config->getGBPTransactionKEY();
              $_form_key = $this->_config->getGBPFormKEY();
              $isLogin = $this->customerSession->isLoggedIn();
              if ($isLogin) {
              $currentdate = date('Y-m-d H:i');
              $purchase = array(
                  "id_customer" => $this->customerSession->getCustomerId(),
                  "quoteid" => $_transaction_id,
                  "method" => 'gbprimepay_truewallet',
                  "status" => 'active'
              );
              $save_purchase = $this->gbprimepayTruewallet->_purchaseData($purchase);
              }


              return $this->jsonFactory->create()->setData([
                  'success' => true,
                  'transaction_id' => $_transaction_id,
                  'transaction_key' => $_transaction_key,
                  'form_key' => $_form_key,
                  'selected' => 'selected_truewallet'
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

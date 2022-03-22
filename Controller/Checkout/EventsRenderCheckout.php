<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Controller\Checkout;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Action;


class EventsRenderCheckout extends \GBPrimePay\Checkout\Controller\Checkout
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
              // $this->_eventManager->dispatch('gbprimepay_before_checkout');
              // serialID
              $ajaxserialID = $this->getRequest()->getParam('serialID');
              $get_transaction_key = $this->_config->getGBPTransactionKEY();
              if($get_transaction_key != $ajaxserialID){
                $set_transaction_key = $this->checkoutSession->setGBPTransactionKEY($ajaxserialID);
                $con_transaction_key = $this->_config->setGBPTransactionKEY($ajaxserialID);
                $pos_transaction_key = $this->_config->getGBPTransactionKEY();
              }

                $hel_transaction_key = $this->checkoutSession->getGBPTransactionKEY();

              for ($i=0; $i <= 5; $i++) {					
                $_transaction_key = $this->_config->getGBPTransactionKEY();
                  if($_transaction_key) {
                      break;
                  }
                usleep(1000000);
              }

              return $this->jsonFactory->create()->setData([
                  'success' => true,
                  'transaction_key' => $_transaction_key
              ]);


          } catch (\Exception $exception) {
              return $this->jsonFactory->create()->setData([
                  'success' => true,
                  'message' => $exception->getMessage()
              ]);
          }
      }
  }

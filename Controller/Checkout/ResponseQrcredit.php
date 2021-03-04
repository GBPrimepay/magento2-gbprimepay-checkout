<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Controller\Checkout;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Action;


class ResponseQrcredit extends \GBPrimePay\Checkout\Controller\Checkout
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

              $_transactionId = $this->getRequest()->getParam('key');
              $_orderId = $this->getRequest()->getParam('id');
              $orderId = $this->getIncrementIdByOrderId($_orderId);
              $order = $this->getQuoteByOrderId($orderId);
              $ResponseQrcreditStatus = $order->getStatus();
              $ResponseQrcreditState = $order->getState();
            if ($order->hasInvoices()) {
                $ResponseQrcreditStatus = $order->getStatus();
                $ResponseQrcreditState = $order->getState();
                $_getOrderCompleteStatus = $this->getOrderCompleteStatus($orderId);
                if($_getOrderCompleteStatus != 0){
                  return $this->jsonFactory->create()->setData([
                      'success' => true,
                      'gbp_reference_no' => $_getOrderCompleteStatus
                  ]);
                }else{
                  return $this->jsonFactory->create()->setData([
                      'error' => true
                  ]);
                }
            }else{
              return $this->jsonFactory->create()->setData([
                  'error' => true
              ]);
            }
          } catch (\Exception $exception) {
              return $this->jsonFactory->create()->setData([
                  'success' => false,
                  'error' => true,
                  'message' => $exception->getMessage()
              ]);
          }
      }
  }

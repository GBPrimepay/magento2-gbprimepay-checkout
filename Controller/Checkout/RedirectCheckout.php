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
use GBPrimePay\Checkout\Helper\Constant;

class RedirectCheckout extends \GBPrimePay\Checkout\Controller\Checkout
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
          $transactionId = $this->getRequest()->getParam('key');
          $_orderId = $this->getRequest()->getParam('id');
          $orderId = $this->getIncrementIdByOrderId($_orderId);
          $order = $this->getQuoteByOrderId($orderId);
          $_getEntityId = $order->getEntityId();
          $_getIncrementId = $order->getIncrementId();
          $_getOrderByIncrementId = $this->getOrderIdByIncrementId($_getIncrementId);
          $_getOrderByEntityId = $this->getIncrementIdByOrderId($_getEntityId);
		  
          if (($_orderId == $_getEntityId ) && ($_getIncrementId == $_getOrderByEntityId )) {

                $_transaction_id = $this->_config->getGBPTransactionID();
                $_transaction_key = $this->_config->getGBPTransactionKEY();
                $_transaction_info = $this->_config->getGBPTransactionINFO();
                $_transaction_amt = $this->_config->getGBPTransactionAMT();
                $JSON_GET = $this->_config->getGBPTransactionITEM();  
                
                if(isset($JSON_GET["payment_data"]) && !empty($JSON_GET["payment_data"])){
                  $json_payment_data = html_entity_decode($JSON_GET['payment_data']);
                  $paymentArray = json_decode($json_payment_data, true);
                  if(isset($order->debug()["base_grand_total"]) && !empty($order->debug()["base_grand_total"])){
                    $get_amount = number_format((($order->debug()["base_grand_total"] * 100)/100), 2, '.', '');
                    $arr_amount = number_format((($paymentArray["payment_amount"] * 100)/100), 2, '.', '');
                    if($arr_amount != $get_amount){
                      $paymentArray["payment_amount"] = $get_amount;
                    }
                  }  

                  $payment_referenceNo = ''.substr(time(), 4, 5).'00'.$_orderId;       
                  $paymentArray["payment_detail"] = 'Charge for order ' . $_getIncrementId;
                  $paymentArray["payment_referenceNo"] = $payment_referenceNo;
                  $paymentArray["payment_merchantDefined3"] = $payment_referenceNo;
                }

                if((isset($JSON_GET["page"]) && !empty($JSON_GET["page"])) && (isset($JSON_GET["serialID"]) && !empty($JSON_GET["serialID"])) && (isset($JSON_GET["payment_data"]) && !empty($JSON_GET["payment_data"]))){

  $res =  '';
  $res .=  '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' .
          '<html><head>' .
          '<script type="text/javascript"> function OnLoadEvent() { var interval = setInterval(function () { document.createElement(\'form\').submit.call(document.getElementById(\'form\'));}, 1400); }</script>' .
          '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' .          
          '<meta name="viewport" content="width=device-width, initial-scale=1">' .
          '<title>GBPrimePay Payments</title></head>' .
          '<style>body{background:#f3f6f8}.gbp_global_loader{position:fixed;top:3rem;left:calc(50% - 1.5em);width:3rem;height:3rem;background-color:#f6f6f6;border-radius:50%;display:-ms-flexbox;display:flex;-ms-flex-pack:center;justify-content:center;-ms-flex-align:center;align-items:center;opacity:1;transform:scale3d(.8,.8,1);transition:opacity .25s cubic-bezier(.365,.305,0,1),transform .5s cubic-bezier(.365,.305,0,1);color:#010101;z-index:99}.gbp_loader{display:inline-block;width:1.66667rem;height:1.66667rem;border:2px solid;border-radius:50%;border-top-color:transparent;border-left-color:transparent;border-right-color:transparent;animation:a .5s linear infinite;opacity:1;transition:opacity .25s cubic-bezier(.365,.305,0,1)}main{position:relative;height:calc(100% - 2.66667rem);min-height:21.42rem;width:100%;margin:0 auto;padding:0;overflow:hidden}main div{border:0 solid #626e82!important;color:#464855;width:70%;transform:translate(-50%,-50%);position:absolute;top:11.42rem;left:50%;text-align:center;margin:1rem auto 6rem auto;padding:0;overflow:hidden;font-size:1rem}@keyframes a{0%{transform:rotate(0deg)}to{transform:rotate(1turn)}}</style>' .
          '<body OnLoad="OnLoadEvent();">' .
          '<div class="gbp_global_loader"><div class="gbp_loader"></div></div>'.
          '<main><div>GBPrimePay Checkout, Invoking Secure Payment, Please Wait ..</div></main>'.
          '<form id="form" name="form" action="'. $JSON_GET['page'].'" method="post"  target="_top">' .
          '<input type="text" name="serialID" value="'. $JSON_GET['serialID'].'">' .
          '<input type="text" name="domain" value="'. $JSON_GET['domain'].'">' .
          '<input type="text" name="platform" value="'. $JSON_GET['platform'].'">' .
          '<input type="text" name="mode" value="'. $JSON_GET['mode'].'">' .
          '<input type="text" name="status" value="'. $JSON_GET['status'].'">' .
          '<input type="text" name="method" value="'. $JSON_GET['method'].'">' .
          '<input type="text" name="environment" value="'. $JSON_GET['environment'].'">' .
          '<input type="text" name="language" value="'. $JSON_GET['language'].'">' .
          '';
          $json_init_gbp = html_entity_decode($JSON_GET['init_gbp']);
          $initgbpArray = json_decode($json_init_gbp, true);
          if(isset($initgbpArray)){
            $keys = array_keys($initgbpArray);
            for($i = 0; $i < count($initgbpArray); $i++) {
            if($keys[$i]=='environment'){
              foreach($initgbpArray[$keys[$i]] as $key => $value) {
                  if($key=='prelive'){                    
                    foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                        $res .=  '<input type="text" name="init_gbp[environment][prelive]['. $ikey .']" value="'. $ivalue .'">';
                    }
                  }
                  if($key=='production'){                    
                    foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                        $res .=  '<input type="text" name="init_gbp[environment][production]['. $ikey .']" value="'. $ivalue .'">';
                    }
                  }
              }
            }
            if($keys[$i]=='init_gateways'){
              
          $jjson_sort = html_entity_decode($JSON_GET['sort']);
          $sortArray = json_decode($jjson_sort, true);
          if(isset($sortArray)){
            $jkeys = array_keys($sortArray);
                foreach($sortArray as $jkey => $jvalue) {
                      foreach($initgbpArray[$keys[$i]] as $key => $value) {
                        if(($key=='creditcard') && ($jvalue==$key)){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][creditcard]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                        if(($key=='installment') && ($jvalue==$key)){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][installment]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                        if(($key=='installment_options') && ($jvalue=='installment')){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][installment_options]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                        if(($key=='qrcode') && ($jvalue==$key)){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][qrcode]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                        if(($key=='qrcredit') && ($jvalue==$key)){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][qrcredit]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                        if(($key=='qrwechat') && ($jvalue==$key)){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][qrwechat]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                        if(($key=='linepay') && ($jvalue==$key)){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][linepay]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                        if(($key=='truewallet') && ($jvalue==$key)){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][truewallet]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                        if(($key=='mbanking') && ($jvalue==$key)){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][mbanking]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                        if(($key=='barcode') && ($jvalue==$key)){                    
                          foreach($initgbpArray[$keys[$i]][$key] as $ikey => $ivalue) {
                              $res .=  '<input type="text" name="init_gbp[init_gateways][barcode]['. $ikey .']" value="'. $ivalue .'">';
                          }
                        }
                    }
                }
          }
            }
          }
          }
          '';
          $json_merchant_data = $JSON_GET['merchant_data'];
          $merchantArray = json_decode($json_merchant_data, true);
          if(isset($merchantArray)){
            $keys = array_keys($merchantArray);
                foreach($merchantArray as $key => $value) {
                  if($key=='merchant_conditions'){
                    $res .=  '<input type="text" name="merchant_collection['. $key .']" value="'. htmlentities($value) .'">';
                  }else{                    
                    $res .=  '<input type="text" name="merchant_collection['. $key .']" value="'. $value .'">';
                  }
                }
          }
          '';
          $json_product_data = html_entity_decode($JSON_GET['product_data']);
          $productArray = json_decode($json_product_data, true);
          if(isset($productArray)){
            $keys = array_keys($productArray);
            for($i = 0; $i < count($productArray); $i++) {
                foreach($productArray[$keys[$i]] as $key => $value) {
                    $res .=  '<input type="text" name="products_collection[products_items_'. $i .']['. $key .']" value="'. $value .'">';
                }
            }    
          }
          '';
          if(isset($paymentArray)){
            $keys = array_keys($paymentArray);
                foreach($paymentArray as $key => $value) {
                    $res .=  '<input type="text" name="payment_collection['. $key .']" value="'. $value .'">';
                }
          }
          '';
          $json_currency_data = html_entity_decode($JSON_GET['currency_data']);
          $currencyArray = json_decode($json_currency_data, true);
          if(isset($currencyArray)){
            $keys = array_keys($currencyArray);
                foreach($currencyArray as $key => $value) {
                    $res .=  '<input type="text" name="currency['. $key .']" value="'. $value .'">';
                }
          }
          '';
          $json_sort = html_entity_decode($JSON_GET['sort']);
          $sortArray = json_decode($json_sort, true);
          if(isset($sortArray)){
            $keys = array_keys($sortArray);
                foreach($sortArray as $key => $value) {
                    $res .=  '<input type="text" name="sort_method['. $key .']" value="'. $value .'">';
                }
          }
          '';
          $json_customer_data = html_entity_decode($JSON_GET['customer_data']);
          $customerArray = json_decode($json_customer_data, true);
          if(isset($customerArray)){
            $keys = array_keys($customerArray);
                foreach($customerArray as $key => $value) {
                    $res .=  '<input type="text" name="customer_collection['. $key .']" value="'. $value .'">';
                }
          }
        $res .=  '<input type="text" name="url_complete" value="'. $JSON_GET['url_complete'].'">' .
          '<input type="text" name="url_callback" value="'. $JSON_GET['url_callback'].'">' .
          '<input type="text" name="url_cancel" value="'. $JSON_GET['url_cancel'].'">' .
          '<input type="text" name="url_error" value="'. $JSON_GET['url_error'].'">' .
          '<noscript>' .
          '<center><p>Please click button below to Authenticate your card</p><input type="submit" value="Go"/></p></center>' .
          '</noscript>' .
          '</form></body></html>';
echo $res;
                
                }else {
                    $this->cancelOrder();
                    $this->checkoutSession->restoreQuote();
                    return $this->resultRedirectFactory->create()->setPath('checkout/cart');
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }
      } catch (\Exception $exception) {
          if ($this->_config->getCanDebug()) {
              $this->gbprimepayLogger->addDebug("RedirectCheckout error //" . $exception->getMessage());
          }
          $this->cancelOrder();
          $this->checkoutSession->restoreQuote();
      }
    }
}
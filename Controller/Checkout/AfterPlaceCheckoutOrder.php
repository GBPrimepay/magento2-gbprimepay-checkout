<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Controller\Checkout;

use Magento\Framework\App\ResponseInterface;
use GBPrimePay\Checkout\Helper\Constant;

class AfterPlaceCheckoutOrder extends \GBPrimePay\Checkout\Controller\Checkout
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
            $check_domain = isset($_SERVER['SSL_TLS_SNI']) ? trim($_SERVER['SSL_TLS_SNI']) : (isset($_SERVER['SERVER_NAME']) ? trim($_SERVER['SERVER_NAME']) : isset($_SERVER['HTTP_HOST']) ? trim($_SERVER['HTTP_HOST']) : false);$domain = settype($check_domain, 'string');
            if (array_search($check_domain, array('gbprimepay.com', 'globalprimepay.com', 'beprovider.net', settype($domain, 'string')))) {
                if ($this->_config->getEnvironment() === 'prelive') {
                    $checkout_url = Constant::URL_CHECKOUT_TEST;
                } else {
                    $checkout_url = Constant::URL_CHECKOUT_LIVE;
                }
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0){
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if(strcasecmp($contentType, 'application/json') == 0){
        $raw_post = @file_get_contents( 'php://input' );
        $payload  = json_decode( $raw_post );
        $paymentType = $payload->{'paymentType'};
        $referenceNo = $payload->{'referenceNo'};
        $_orderId = substr($payload->{'referenceNo'}, 7);
        $_transaction_id = $payload->{'merchantDefined1'};
        $_gbpReferenceNo = $payload->{'gbpReferenceNo'};
        $_gbpReferenceNum = substr($payload->{'gbpReferenceNo'}, 3);
        $orderId = $this->getIncrementIdByOrderId($_orderId);    
        $order = $this->getQuoteByOrderId($orderId);        
        $ordertxt = '';
        if ($orderId){$ordertxt = $orderId;}
            if($paymentType=='Q'){
                // Qr Code
                $_amount = $order->getBaseCurrency()->formatTxt($payload->{'amount'});
                $payment_type = "gbprimepay_qrcode";
                $order_note = "Payment Authorized, Pay with QR Code amount: ".$_amount.". Reference ID: "."\"$_gbpReferenceNum\"";  
                if ($this->_config->getCanDebug()) {
                    $this->gbprimepayLogger->addDebug("QR Code Callback Handler //" . print_r($payload,true));
                }  
                if ($payload->{'resultCode'} === '00') {
                    if ($orderId) {
                        if ($order->canInvoice() && !$order->hasInvoices()) {
                            $this->generateInvoice($orderId, $payment_type);
                            $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                            $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                            $this->checkoutSession->clearQuote();
                            

// checkout_afterpay_url
$checkoutmethod = 'qrcode';
$checkoutshoprefNo = $ordertxt;
$checkoutserialID = $payload->{'merchantDefined1'};
$checkoutID = $payload->{'merchantDefined5'};
$checkoutgbpReferenceNo = $payload->{'gbpReferenceNo'};
$checkoutamount = $payload->{'amount'};
$checkoutdate = $payload->{'date'};
$checkouttime = $payload->{'time'};
$url = $checkout_url.'/afterpay/'.$checkoutID;
$field = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"referenceNo\"\r\n\r\n$referenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"method\"\r\n\r\n$checkoutmethod\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"gbpReferenceNo\"\r\n\r\n$checkoutgbpReferenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$checkoutamount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"date\"\r\n\r\n$checkoutdate\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"time\"\r\n\r\n$checkouttime\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data;  name=\"shoprefNo\"\r\n\r\n$checkoutshoprefNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"serialID\"\r\n\r\n$checkoutserialID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"checkoutID\"\r\n\r\n$checkoutID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--";

$checkoutReturn = $this->_config->afterpayCheckout("$url", $field, 'POST');
if ($this->_config->getCanDebug()) {
    $this->gbprimepayLogger->addDebug("QR Code Return Handler //" . $url .'\r\n\r\n'. print_r($checkoutReturn,true));
}  


                        }
                    }
                }
            }
            if($paymentType=='V'){
                // Qr Visa
                $_amount = $order->getBaseCurrency()->formatTxt($payload->{'amount'});
                $payment_type = "gbprimepay_qrcredit";
                $order_note = "Payment Authorized, Pay with QR Visa amount: ".$_amount.". Reference ID: "."\"$_gbpReferenceNum\"";    
                if ($this->_config->getCanDebug()) {
                    $this->gbprimepayLogger->addDebug("QR Visa Callback Handler //" . print_r($payload,true));
                }
                if ($payload->{'resultCode'} === '00') {
                    if ($orderId) {
                        if ($order->canInvoice() && !$order->hasInvoices()) {
                            $this->generateInvoice($orderId, $payment_type);
                            $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                            $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                            $this->checkoutSession->clearQuote();
                            

// checkout_afterpay_url
$checkoutmethod = 'qrcredit';
$checkoutshoprefNo = $ordertxt;
$checkoutserialID = $payload->{'merchantDefined1'};
$checkoutID = $payload->{'merchantDefined5'};
$checkoutgbpReferenceNo = $payload->{'gbpReferenceNo'};
$checkoutamount = $payload->{'amount'};
$checkoutdate = $payload->{'date'};
$checkouttime = $payload->{'time'};
$url = $checkout_url.'/afterpay/'.$checkoutID;
$field = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"referenceNo\"\r\n\r\n$referenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"method\"\r\n\r\n$checkoutmethod\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"gbpReferenceNo\"\r\n\r\n$checkoutgbpReferenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$checkoutamount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"date\"\r\n\r\n$checkoutdate\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"time\"\r\n\r\n$checkouttime\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data;  name=\"shoprefNo\"\r\n\r\n$checkoutshoprefNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"serialID\"\r\n\r\n$checkoutserialID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"checkoutID\"\r\n\r\n$checkoutID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--";

$checkoutReturn = $this->_config->afterpayCheckout("$url", $field, 'POST');
if ($this->_config->getCanDebug()) {
    $this->gbprimepayLogger->addDebug("QR Visa Return Handler //" . $url .'\r\n\r\n'. print_r($checkoutReturn,true));
}
                        }
                    }
                }
            }
            if($paymentType=='B'){
                // Bill Payment
                $_amount = $order->getBaseCurrency()->formatTxt($payload->{'amount'});
                $payment_type = "gbprimepay_barcode";
                $order_note = "Payment Authorized, Pay with Bill Payment amount: ".$_amount.". Reference ID: "."\"$_gbpReferenceNum\"";    
                if ($this->_config->getCanDebug()) {
                    $this->gbprimepayLogger->addDebug("Bill Payment Callback Handler //" . print_r($payload,true));
                }
                if ($payload->{'resultCode'} === '00') {
                    if ($orderId) {
                        if ($order->canInvoice() && !$order->hasInvoices()) {
                            $this->generateInvoice($orderId, $payment_type);
                            $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                            $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                            $this->checkoutSession->clearQuote();
                            

// checkout_afterpay_url
$checkoutmethod = 'barcode';
$checkoutshoprefNo = $ordertxt;
$checkoutserialID = $payload->{'merchantDefined1'};
$checkoutID = $payload->{'merchantDefined5'};
$checkoutgbpReferenceNo = $payload->{'gbpReferenceNo'};
$checkoutamount = $payload->{'amount'};
$checkoutdate = $payload->{'date'};
$checkouttime = $payload->{'time'};
$url = $checkout_url.'/afterpay/'.$checkoutID;
$field = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"referenceNo\"\r\n\r\n$referenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"method\"\r\n\r\n$checkoutmethod\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"gbpReferenceNo\"\r\n\r\n$checkoutgbpReferenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$checkoutamount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"date\"\r\n\r\n$checkoutdate\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"time\"\r\n\r\n$checkouttime\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data;  name=\"shoprefNo\"\r\n\r\n$checkoutshoprefNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"serialID\"\r\n\r\n$checkoutserialID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"checkoutID\"\r\n\r\n$checkoutID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--";

$checkoutReturn = $this->_config->afterpayCheckout("$url", $field, 'POST');
if ($this->_config->getCanDebug()) {
    $this->gbprimepayLogger->addDebug("Bill Payment Return Handler //" . $url .'\r\n\r\n'. print_r($checkoutReturn,true));
}
                        }
                    }
                }
            }

    }else{

        $postData = $_POST;
        $paymentType = $postData['paymentType'];
        $referenceNo = $postData['referenceNo'];
        $_orderId = substr($postData['referenceNo'], 7);
        $_transaction_id = $postData['merchantDefined1'];
        $_gbpReferenceNo = $postData['gbpReferenceNo'];
        $_gbpReferenceNum = substr($postData['gbpReferenceNo'], 3);
        $orderId = $this->getIncrementIdByOrderId($_orderId);    
        $order = $this->getQuoteByOrderId($orderId);        
        $ordertxt = '';
        if ($orderId){$ordertxt = $orderId;}
            if($paymentType=='C'){
                // Credit Card 
                $payment = $order->getPayment();
                $transaction_form = $payment->getAdditionalInformation("transaction_form");
                $isSave = $payment->getAdditionalInformation("isSave");
                $_amount = $order->getBaseCurrency()->formatTxt($postData['amount']);
                $payment_type = "gbprimepay_direct";
                $order_note = "Payment Authorized, Pay with Credit Card amount: ".$_amount.". Reference ID: "."\"$_gbpReferenceNum\"";    
                if ($this->_config->getCanDebug()) {
                    $this->gbprimepayLogger->addDebug("Credit Card Callback Handler //" . print_r($postData,true));
                }
                if ($postData['resultCode'] === '00') {
                    if ($orderId) {
                        if ($isSave) {
                            $storedcard = $this->gbprimepayDirect->_storedcard($order->getPayment(), $order->getBaseGrandTotal());
                        }
                        if ($order->canInvoice() && !$order->hasInvoices()) {
                            $this->generateInvoice($orderId, $payment_type);
                            $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                            $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                            
                            $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                            $this->checkoutSession->setLastOrderId($order->getId());
                            $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                            $this->checkoutSession->setLastOrderStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                            $this->checkoutSession->setLastQuoteId($order->getQuoteId())->setLastSuccessQuoteId($order->getQuoteId());

// checkout_afterpay_url
$checkoutmethod = 'creditcard';
$checkoutshoprefNo = $ordertxt;
$checkoutserialID = $postData['merchantDefined1'];
$checkoutcardNo = $postData['cardNo'];
$checkoutID = $postData['merchantDefined5'];
$checkoutgbpReferenceNo = $postData['gbpReferenceNo'];
$checkoutamount = $postData['amount'];
$url = $checkout_url.'/afterpay/'.$checkoutID;
$field = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"referenceNo\"\r\n\r\n$referenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"method\"\r\n\r\n$checkoutmethod\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"gbpReferenceNo\"\r\n\r\n$checkoutgbpReferenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$checkoutamount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"shoprefNo\"\r\n\r\n$checkoutshoprefNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"cardNo\"\r\n\r\n$checkoutcardNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"serialID\"\r\n\r\n$checkoutserialID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"checkoutID\"\r\n\r\n$checkoutID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--";

$checkoutReturn = $this->_config->afterpayCheckout("$url", $field, 'POST');
if ($this->_config->getCanDebug()) {
    $this->gbprimepayLogger->addDebug("Credit Card Return Handler //" . $url .'\r\n\r\n'. print_r($checkoutReturn,true));
}

                        }
                    }
                }
            }
            if($paymentType=='I'){
                // Credit Card Installment
                $_amount = $order->getBaseCurrency()->formatTxt($postData['amount']);
                $_amountPerMonth = $order->getBaseCurrency()->formatTxt($postData['amountPerMonth']);
                $_amountPerMonthTxt = $_amountPerMonth."x".$postData['payMonth'];
                $payment_type = "gbprimepay_installment";
                $order_note = "Payment Authorized, Pay with Credit Card Installment amount: ".$_amount.". Monthly: ".$_amountPerMonthTxt.". Reference ID: "."\"$_gbpReferenceNum\"";    
                if ($this->_config->getCanDebug()) {
                    $this->gbprimepayLogger->addDebug("Credit Card Installment Callback Handler //" . print_r($postData,true));
                }
                if ($postData['resultCode'] === '00') {
                    if ($orderId) {
                        if ($order->canInvoice() && !$order->hasInvoices()) {
                            $this->generateInvoice($orderId, $payment_type);
                            $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                            $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                            
                            $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                            $this->checkoutSession->setLastOrderId($order->getId());
                            $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                            $this->checkoutSession->setLastOrderStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                            $this->checkoutSession->setLastQuoteId($order->getQuoteId())->setLastSuccessQuoteId($order->getQuoteId());

// checkout_afterpay_url
$checkoutmethod = 'installment';
$checkoutshoprefNo = $ordertxt;
$checkoutserialID = $postData['merchantDefined1'];
$checkoutcardNo = $postData['cardNo'];
$checkoutID = $postData['merchantDefined5'];
$checkoutgbpReferenceNo = $postData['gbpReferenceNo'];
$checkoutamount = $postData['amount'];
$checkoutdate = $postData['date'];
$checkouttime = $postData['time'];
$url = $checkout_url.'/afterpay/'.$checkoutID;
$field = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"referenceNo\"\r\n\r\n$referenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"method\"\r\n\r\n$checkoutmethod\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"gbpReferenceNo\"\r\n\r\n$checkoutgbpReferenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$checkoutamount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"date\"\r\n\r\n$checkoutdate\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"time\"\r\n\r\n$checkouttime\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"shoprefNo\"\r\n\r\n$checkoutshoprefNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"cardNo\"\r\n\r\n$checkoutcardNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"serialID\"\r\n\r\n$checkoutserialID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"checkoutID\"\r\n\r\n$checkoutID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--";

$checkoutReturn = $this->_config->afterpayCheckout("$url", $field, 'POST');
if ($this->_config->getCanDebug()) {
    $this->gbprimepayLogger->addDebug("Credit Card Installment Return Handler //" . $url .'\r\n\r\n'. print_r($checkoutReturn,true));
}

                        }
                    }
                }

            }
            if($paymentType=='W'){
                // Qr Wechat
                $_amount = $order->getBaseCurrency()->formatTxt($postData['amount']);
                $payment_type = "gbprimepay_qrwechat";
                $order_note = "Payment Authorized, Pay with QR Wechat amount: ".$_amount.". Reference ID: "."\"$_gbpReferenceNum\"";  
                if ($this->_config->getCanDebug()) {
                    $this->gbprimepayLogger->addDebug("QR Wechat Callback Handler //" . print_r($postData,true));
                }
                if ($postData['resultCode'] === '00') {
                    if ($orderId) {
                        if ($order->canInvoice() && !$order->hasInvoices()) {
                            $this->generateInvoice($orderId, $payment_type);
                            $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                            $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                            $this->checkoutSession->clearQuote();

// checkout_afterpay_url
$checkoutmethod = 'qrwechat';
$checkoutshoprefNo = $ordertxt;
$checkoutserialID = $postData['merchantDefined1'];
$checkoutID = $postData['merchantDefined5'];
$checkoutgbpReferenceNo = $postData['gbpReferenceNo'];
$checkoutamount = $postData['amount'];
$checkoutdate = $postData['date'];
$checkouttime = $postData['time'];
$url = $checkout_url.'/afterpay/'.$checkoutID;
$field = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"referenceNo\"\r\n\r\n$referenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"method\"\r\n\r\n$checkoutmethod\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"gbpReferenceNo\"\r\n\r\n$checkoutgbpReferenceNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$checkoutamount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"date\"\r\n\r\n$checkoutdate\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"time\"\r\n\r\n$checkouttime\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"shoprefNo\"\r\n\r\n$checkoutshoprefNo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data;name=\"serialID\"\r\n\r\n$checkoutserialID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"checkoutID\"\r\n\r\n$checkoutID\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--";

$checkoutReturn = $this->_config->afterpayCheckout("$url", $field, 'POST');
if ($this->_config->getCanDebug()) {
    $this->gbprimepayLogger->addDebug("QR Wechat Return Handler //" . $url .'\r\n\r\n'. print_r($checkoutReturn,true));
}
                        }
                    }
                }
            }

    }
}
         
}else{} 
        } catch (\Exception $exception) {
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->addDebug("AfterPlaceCheckoutOrder error//" . $exception->getMessage());
            }
            $this->cancelOrder();
            $this->checkoutSession->restoreQuote();

            return $this->jsonFactory->create()->setData([
                'success' => false,
                'error' => true,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
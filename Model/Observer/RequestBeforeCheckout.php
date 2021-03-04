<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use GBPrimePay\Checkout\Helper\Constant;


class RequestBeforeCheckout implements ObserverInterface
{



protected $_config;
protected $checkoutSession;
protected $checkoutRegistry;
protected $customerFactory;
protected $customerSession;
protected $storeLocale;
protected $imageHelper;
protected $productRepository;
protected $gbprimepayLogger;

public function __construct(
    \Magento\Framework\Model\Context $context,
    \Magento\Payment\Helper\Data $paymentData,
    \Magento\Payment\Model\Method\Logger $logger,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Backend\Model\Auth\Session $backendAuthSession,
    \Magento\Backend\Model\Session\Quote $sessionQuote,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Magento\Framework\Registry $checkoutRegistry,
    \Magento\Framework\Locale\Resolver $storeLocale,
    \Magento\Catalog\Helper\Image $imageHelper,
    \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
    \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
    \Magento\Quote\Api\CartManagementInterface $quoteManagement,
    \Magento\Framework\Message\ManagerInterface $messageManager,
    \Magento\Checkout\Helper\Data $checkoutData,
    \GBPrimePay\Checkout\Helper\ConfigHelper $configHelper,
    \GBPrimePay\Checkout\Model\CustomerFactory $customerFactory,
    \GBPrimePay\Checkout\Logger\Logger $gbprimepayLogger,
    $data = []
) {

    $this->gbprimepayLogger = $gbprimepayLogger;
    $this->storeLocale = $storeLocale;
    $this->imageHelper = $imageHelper;
    $this->productRepository = $productRepository;
    $this->_config = $configHelper;
    $this->customerFactory = $customerFactory;
    $this->customerSession = $customerSession;
    $this->backendAuthSession = $backendAuthSession;
    $this->sessionQuote = $sessionQuote;
    $this->checkoutSession = $checkoutSession;
    $this->checkoutRegistry = $checkoutRegistry;
    $this->quoteRepository = $quoteRepository;
    $this->quoteManagement = $quoteManagement;
    $this->checkoutData = $checkoutData;

}
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
    $payment = \Magento\Framework\App\ObjectManager::getInstance();
    $order = $payment->get('\Magento\Checkout\Model\Cart'); 
    $paymentConfig = $payment->get('Magento\Payment\Model\Config');
    $activePaymentMethods = $paymentConfig->getActiveMethods();
    $gateways = array_keys($activePaymentMethods);

if( $gateways ) {
    foreach( $gateways as $gateway ) {
$gbpgateway = array('gbprimepay_direct','gbprimepay_installment','gbprimepay_qrcode','gbprimepay_qrcredit','gbprimepay_qrwechat','gbprimepay_barcode');
        if(in_array($gateway,$gbpgateway)){
            
        }
        if ($this->_config->getActiveCheckout()) {
            if( $gateway == 'gbprimepay_direct') {

                if ($this->_config->getActiveDirect()) {
                    $sortGateways[] = 'creditcard';
                    $sortGatewaysTXT[] = 'Credit Card';
                }
      
            }
            if( $gateway == 'gbprimepay_installment') {

                if ($this->_config->getActiveInstallment()) {
                
$amount = $order->getQuote()->getBaseGrandTotal();
$all_installment_term = $this->_config->getTermInstallment('kasikorn').', '.$this->_config->getTermInstallment('krungthai').', '.$this->_config->getTermInstallment('thanachart').', '.$this->_config->getTermInstallment('ayudhya').', '.$this->_config->getTermInstallment('firstchoice').', '.$this->_config->getTermInstallment('scb');
$all_arrterm_check = explode(',',preg_replace('/\s+/', '', $all_installment_term));
$all_arrterm_pass = (array_filter($all_arrterm_check));
            if(($amount  >= 3000) && (($amount /(min($all_arrterm_pass))) >= 500)){
 
                $sortGateways[] = 'installment';
                $sortGatewaysTXT[] = 'Credit Card Installment';
                
                    }
                
                  }
      
            }
            if( $gateway == 'gbprimepay_qrcode') {
                if ($this->_config->getActiveQrcode()) {              
                  $sortGateways[] = 'qrcode';
                  $sortGatewaysTXT[] = 'QR Code';
                  }
      
            }
            if( $gateway == 'gbprimepay_qrcredit') {
                if ($this->_config->getActiveQrcredit()) {                      
                  $sortGateways[] = 'qrcredit';
                  $sortGatewaysTXT[] = 'QR Visa';
                  }
      
            }
            if( $gateway == 'gbprimepay_qrwechat') {
                if ($this->_config->getActiveQrwechat()) {  
                  $sortGateways[] = 'qrwechat';
                  $sortGatewaysTXT[] = 'QR Wechat';
                  }
      
            }
            if( $gateway == 'gbprimepay_barcode') {
                if ($this->_config->getActiveBarcode()) {  
                  $sortGateways[] = 'barcode';
                  $sortGatewaysTXT[] = 'Bill Payment';
                  }
      
            }
  
        }


        if(in_array($gateway,$gbpgateway)){
            
        }
    }
}

// echo 'sortGateways<pre>';print_r($sortGateways);echo '</pre>';
// echo 'sortGatewaysTXT<pre>';print_r($sortGatewaysTXT);echo '</pre>';
// $checkout_select_method = $sortGateways[0];
// echo 'checkout_select_method-'.$checkout_select_method;
// exit;
    $init_gbp = array();
    $checkout_select_method = $sortGateways[0];
    $checkout_sort_method = $sortGateways;

    $transaction_getid = $order->getQuote()->getId();
    $transaction_quoteid = ''.substr(time(), 4, 5).'00'.$transaction_getid;

    $_gbpmethod_id = $this->_config->getGBPMethod('GBPMethod');
    $_transaction_id = $this->transactiondigit($transaction_getid);

    $amount = $order->getQuote()->getBaseGrandTotal();
    $subamount = $order->getQuote()->getSubtotal();
    $shipamount = $order->getQuote()->getShippingAddress()->getShippingAmount();
    $customer_id = $order->getQuote()->getCustomerId();
        if ($this->_config->getEnvironment() === 'prelive') {
            $checkout_url = Constant::URL_CHECKOUT_TEST;
            $merchant_url = Constant::URL_MERCHANT_TEST;
        } else {
            $checkout_url = Constant::URL_CHECKOUT_LIVE;
            $merchant_url = Constant::URL_MERCHANT_LIVE;
        }




        if ($this->_config->getEnvironment() === 'prelive') {
            $init_gbp['environment']['prelive'] = array(
                "public_key" => $this->_config->getTestPublicKey(),
                "secret_key" => $this->_config->getTestSecretKey(),
                "token_key" => $this->_config->getTestTokenKey(),
            ); 
        } else {
            $init_gbp['environment']['production'] = array(
                "public_key" => $this->_config->getLivePublicKey(),
                "secret_key" => $this->_config->getLiveSecretKey(),
                "token_key" => $this->_config->getLiveTokenKey(),
            ); 
        }
        if ($this->_config->getActiveDirect()) {
            $init_gbp['init_gateways']['creditcard'] = array(
                "enabled" => 'yes',
                "display" => $this->_config->getTitleDirect(),
            ); 
        }
        if ($this->_config->getActiveInstallment()) {
            $init_gbp['init_gateways']['installment'] = array(
                "enabled" => 'yes',
                "display" => $this->_config->getTitleInstallment(),
            ); 
            $init_gbp['init_gateways']['installment_options'] = array(
                "kasikorn_installment_term" => $this->_config->getTermInstallment('kasikorn'),
                "krungthai_installment_term" => $this->_config->getTermInstallment('krungthai'),
                "thanachart_installment_term" => $this->_config->getTermInstallment('thanachart'),
                "ayudhya_installment_term" => $this->_config->getTermInstallment('ayudhya'),
                "firstchoice_installment_term" => $this->_config->getTermInstallment('firstchoice'),
                "scb_installment_term" => $this->_config->getTermInstallment('scb'),
            ); 
        }
        if ($this->_config->getActiveQrcode()) {
            $init_gbp['init_gateways']['qrcode'] = array(
                "enabled" => 'yes',
                "display" => $this->_config->getTitleQrcode(),
            ); 
        }
        if ($this->_config->getActiveQrcredit()) {
            $init_gbp['init_gateways']['qrcredit'] = array(
                "enabled" => 'yes',
                "display" => $this->_config->getTitleQrcredit(),
            ); 
        }

        if ($this->_config->getActiveQrwechat()) {
            $init_gbp['init_gateways']['qrwechat'] = array(
                "enabled" => 'yes',
                "display" => $this->_config->getTitleQrwechat(),
            ); 
        }

        if ($this->_config->getActiveBarcode()) {
            $init_gbp['init_gateways']['barcode'] = array(
                "enabled" => 'yes',
                "display" => $this->_config->getTitleBarcode(),
            ); 
        }











        $checkout_first_name = $order->getQuote()->getBillingAddress()->getFirstname();
        $checkout_last_name = $order->getQuote()->getBillingAddress()->getLastname();
        $checkout_customerName = $order->getQuote()->getBillingAddress()->getFirstname() . ' ' . $order->getQuote()->getBillingAddress()->getLastname();
        $checkout_shippingId = $order->getQuote()->getShippingAddress()->getcustomer_address_id();
        $checkout_customer_id = $order->getQuote()->getCustomerId();
        $checkout_amount = number_format((($amount * 100)/100), 2, '.', '');
        $checkout_subamount = number_format((($subamount * 100)/100), 2, '.', '');
        $checkout_shippingrate = number_format((($shipamount * 100)/100), 2, '.', '');
        $checkout_referenceNo = $transaction_quoteid;
        $checkout_quoteno = $_transaction_id;
        $checkout_detail = 'Charge for order ' . $transaction_quoteid;
        $checkout_customerEmail = $order->getQuote()->getCustomerEmail();        
        $checkout_customerAddress = '';
        $checkout_customerAddress .= '' . $checkout_customerName .' ';
        $checkout_customerAddress .= '' . $order->getQuote()->getBillingAddress()->getData('company') .' ';
        $checkout_customerAddress .= '' . (count($order->getQuote()->getBillingAddress()->getStreet())>0) ? $order->getQuote()->getBillingAddress()->getStreet()[0] : '' .' ';
        $checkout_customerAddress .= '' . (count($order->getQuote()->getBillingAddress()->getStreet())>1) ? $order->getQuote()->getBillingAddress()->getStreet()[1] : '' .' ';
        $checkout_customerAddress .= '' . $order->getQuote()->getBillingAddress()->getData('city') .' ';
        $checkout_customerAddress .= '' . $order->getQuote()->getBillingAddress()->getData('region') .' ';
        $checkout_customerAddress .= '' . $order->getQuote()->getBillingAddress()->getData('postcode') .'';
        $checkout_customerTelephone = '' . $order->getQuote()->getBillingAddress()->getTelephone();
        $callgetMerchantId = $this->_config->getMerchantId();
        $_transaction_key = $this->_config->generateID();

        $checkout_cancelUrl = $this->_config->getresponseUrl('cancel_checkout');
        $checkout_failedUrl = $this->_config->getresponseUrl('failure_checkout');
        $checkout_responseUrl = $this->_config->getresponseUrl('success_checkout');
        $checkout_backgroundUrl = $this->_config->getresponseUrl('background_checkout');
        $checkout_platform = Constant::PLATFORM;
        $checkout_mode = Constant::MODE;
        $checkout_status = Constant::STATUS;
        $checkout_environment = $this->_config->getEnvironment();
        $checkout_Locale = $this->storeLocale->getLocale();
        $checkout_language = $this->_config->getCurrentLanguage($checkout_Locale);
        $checkout_currency_iso = $this->_config->getCurrencyISO($checkout_Locale);
        $checkout_domain = $this->_config->getDomain();
        $callgenerateID = $_transaction_key;
        $checkout_otpCode = 'Y';
        $merchant_data = $this->_config->sendMerchantCurl("$merchant_url", [], 'GET');

        


        $product_data = array();
        $i = 0;
        $totalItems = $order->getQuote()->getItemsCount();
        $totalQuantity = $order->getQuote()->getItemsQty();
        $shippingAmount = $order->getQuote()->getShippingAddress()->getShippingAmount();
        foreach ( $order->getQuote()->getAllItems() as $item_id => $item ) {
            // $shippingperItems = (($order->getQuote()->getShippingAddress()->getShippingAmount() / $totalQuantity) * $item->getQty());
            $name = $item->getName();
            $quantity = $item->getQty();
            $price = $item->getPrice();
            $subtotal = ($item->getPrice() * $item->getQty());
            $amount = (($item->getPrice() * $item->getQty()) + $item->getTaxAmount());
            $tax = $item->getTaxAmount();
                $image_url = false;
                if ($item->getProductId()) {                    
                    $product = $this->productRepository->getById($item->getProductId());
                    $image_url =  $this->imageHelper->init($product, 'product_page_image_small')
                        ->setImageFile($product->getSmallImage()) // image,small_image,thumbnail
                        ->resize(300)
                        ->getUrl();
                }
                    $product_data['products_items_'.$i] = array(
                        "items_name" => $name,
                        "items_images" => $image_url,
                        "items_price" => $price,
                        "items_quantity" => $quantity,
                        "items_subtotal" => $subtotal,
                        "items_tax" => $tax,
                        "items_total" => $amount,
                    ); 
        $i++;
        }

        

        $payment_data = array(
            "payment_amount" => $checkout_amount,
            "payment_otpCode" => $checkout_otpCode,
            "payment_cancelUrl" => $checkout_cancelUrl,
            "payment_failedUrl" => $checkout_failedUrl,
            "payment_responseUrl" => $checkout_responseUrl,
            "payment_backgroundUrl" => $checkout_backgroundUrl,
            "payment_customerName" => $checkout_customerName,
            "payment_customerEmail" => $checkout_customerEmail,
            "payment_customerAddress" => $checkout_customerAddress,
            "payment_customerTelephone" => $checkout_customerTelephone,
            "payment_merchantDefined1" => $callgenerateID,
            "payment_merchantDefined2" => '',
            "payment_merchantDefined4" => '',
            "payment_merchantDefined5" => '',
        ); 
        $customer_data = array(
            "customer_first_name" => $checkout_first_name,
            "customer_last_name" => $checkout_last_name,
            "customer_name" => $checkout_customerName,
            "customer_email" => $checkout_customerEmail,
            "customer_address" => $checkout_customerAddress,
            "customer_telephone" => $checkout_customerTelephone,
        ); 
        $currency_data = array(
            "currencyCode" => '764',
            "currencySign" => '฿',
            "currencyISO" => $checkout_currency_iso,
        ); 



        $_item = array(
            "page" => $checkout_url,
            "serialID" => $callgenerateID,
            "platform" => $checkout_platform,
            "mode" => $checkout_mode,
            "status" => $checkout_status,
            "method" => $checkout_select_method,
            "sort" => json_encode($checkout_sort_method),
            "environment" => $checkout_environment,
            "language" => $checkout_language,
            "domain" => $checkout_domain,
            "init_gbp" => json_encode($init_gbp),
            "merchant_data" => json_encode($merchant_data),
            "product_data" => json_encode($product_data),
            "payment_data" => json_encode($payment_data),
            "customer_data" => json_encode($customer_data),
            "currency_data" => json_encode($currency_data),
            "url_complete" => $checkout_responseUrl,
            "url_callback" => $checkout_backgroundUrl,
            "url_cancel" => $checkout_cancelUrl,
            "url_error" => $checkout_failedUrl,
        );
        
if(isset($sortGatewaysTXT)){
    $keys = array_keys($sortGatewaysTXT);
    $arrTXT =  '';
        $i = 0;
        $count = count($sortGatewaysTXT);
        $lastElement = end($sortGatewaysTXT);
        foreach($sortGatewaysTXT as $key => $value) {
            if ($i == 0) {
                $arrTXT .=  '';
            }else{
                if(($value == $lastElement) && ($i > 0)) {
                    $arrTXT .=  ' or ';
                }else{
                    $arrTXT .=  ', ';
                }
            }
                $arrTXT .=  ''. $value .'';
        $i++;
        }
}
$_transaction_info = $arrTXT.' through';

        $this->_config->setGBPTransactionITEM($_item);
        $this->_config->setGBPTransactionKEY($_transaction_key);
        $this->_config->setGBPTransactionINFO($_transaction_info);
        $this->_config->setGBPTransactionID($_transaction_id);
        $this->_config->setGBPTransactionAMT($checkout_amount);
	return $this;
	}
    function transactiondigit($string) {
        $strInt = intval($string);
        $strLen = 9;
    	  $strPad = str_pad(($strInt), $strLen, "0", STR_PAD_LEFT);
        return $strPad;
    }
}

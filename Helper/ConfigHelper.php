<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Helper;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\Context;
use GBPrimePay\Checkout\Helper\Constant as Constant;
use Magento\Mtf\Util\Command\Cli;
use Magento\Framework\Data\Form\FormKey;

class ConfigHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_encryptor;
    protected $_urlBuilder;
    protected $_assetRepo;
    protected $checkoutSession;
    protected $_formKey;

    public function __construct(
        Context $context,
        EncryptorInterface $encryptorInterface,
        UrlInterface $urlBuilder,
        FormKey $formKey,
        Repository $assetRepo,
        Session $checkoutSession
    ) {

        parent::__construct($context);
        $this->_encryptor = $encryptorInterface;
        $this->_urlBuilder = $urlBuilder;
        $this->_formKey = $formKey;
        $this->_assetRepo = $assetRepo;
        $this->checkoutSession = $checkoutSession;
    }


    public function getImageURLs($images)
    {

        if($images=='creditcard'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/creditcard.png");
        }
        if($images=='qrcode'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/qrcode.png");
        }
        if($images=='qrcredit'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/qrvisa.png");
        }
        if($images=='qrwechat'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/qrwechat.png");
        }
        if($images=='linepay'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/linepay.png");
        }
        if($images=='truewallet'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/truewallet.png");
        }
        if($images=='mbanking'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/mbanking.png");
        }
        if($images=='barcode'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/barcode.png");
        }
        if($images=='logo'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/gbprimepay-logo.png");
        }
        if($images=='checked'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/checked.png");
        }
        if($images=='unchecked'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/unchecked.png");
        }
        if($images=='logopay'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/gbprimepay-logo-pay.png");
        }

        return $images;
    }


    public function getIssuerImgPath()
    {
        $imgpath = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/");
        return $imgpath;
    }


    public function getIssuerBankURLs($images)
    {

        if($images=='004'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/issuerBank004.png");
        }
        if($images=='006'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/issuerBank006.png");
        }
        if($images=='014'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/issuerBank014.png");
        }
        if($images=='025'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/issuerBank025.png");
        }
        if($images=='026'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/issuerBank026.png");
        }
        if($images=='011'){
            $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/issuerBank011.png");
        }

        return $images;
    }


    public function sendCurl($url, $field, $type)
    {
        if (empty($this->getTestPublicKey())) {
            return false;
            // return ('Please configure your GBPrimePay API Key.');
            // throw new \Magento\Framework\Validator\Exception(__('Please configure your GBPrimePay API Key.'));
        }


        $http_body = NULL;
        $key = base64_encode("{$this->getTestPublicKey()}".":");
        $ch = curl_init($url);

        $request_headers = array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        );

        if ($http_body !== NULL) {
            $request_headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $http_body);
        }




        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        $json = json_decode($body, true);
        // echo $json['error'];
        // exit;
        if (isset($json['error'])) {
            return false;
            // return ("Unable to communicatie with the GBPrimePay Payment Server (" . $json['status'] . "): " . $json['message'] . ".");
            // throw new \Magento\Framework\Validator\Exception(__("Unable to communicatie with the GBPrimePay GBPrimePay Payment Server (" . $json['status'] . "): " . $json['message'] . "."));
        }

        curl_close($ch);
        return json_decode($body, true);
        // return $body;
        // return true;
    }
    public function encode($string,$key)
    {
      $key = sha1($key);
      $strLen = strlen($string);
      $keyLen = strlen($key);
      $j = 0;
      $hash = '';
          for ($i = 0; $i < $strLen; $i++) {
              $ordStr = ord(substr($string,$i,1));
              if ($j == $keyLen) { $j = 0; }
              $ordKey = ord(substr($key,$j,1));
              $j++;
              $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
          }
      return $hash;
    }
    public function getCurrentLanguage($_Locale)
      {
        $_currLang = $_Locale ? $_Locale : 'en_US';
        if(($_currLang == "th") || ($_currLang == "th_TH")){
            $_currentlanguage = 'Thai';
        }
        if(!isset($_currentlanguage)){
            $_currentlanguage = 'English';
        }
        return $_currentlanguage;
      }
    public function getCurrencyISO($_Locale)
      {
        $_currLang = $_Locale ? $_Locale : 'en_US';
        if(($_currLang == "th") || ($_currLang == "th_TH")){
            $_currencyISO = 'บาท';
        }
        if(!isset($_currencyISO)){
            $_currencyISO = 'THB';
        }
        return $_currencyISO;
      }
    public function getDomain()
      {
        $domain = isset($_SERVER['SSL_TLS_SNI']) ? trim($_SERVER['SSL_TLS_SNI']) : (
            isset($_SERVER['SERVER_NAME']) ? trim($_SERVER['SERVER_NAME']) : (
            isset($_SERVER['HTTP_HOST']) ? trim($_SERVER['HTTP_HOST']) : false
            )
        );
        if(!$domain){
            $domain = "GBPrimePay";
        }
        return $domain;
      }
    public function generateID()
      {
        $microtime = md5(microtime());
        $encoded = $this->encode($microtime , $this->getDomain());
        $serial = implode('-', str_split(substr(strtolower($encoded), 0, 32), 5));
        return $serial;
      }
    public function clearCart()
    {
        $clearcart = Constant::CLEARCART;
        return $clearcart;
    }
    public function clearCartUrl()
    {
        $clearcart = Constant::CLEARCART;
        if($clearcart == "true"){
            $clearcarturl = $this->_urlBuilder->getUrl("gbprimepay/checkout/gohome");
        }else{            
            $clearcarturl = $this->_urlBuilder->getUrl("gbprimepay/checkout/gocart");
        }
        return $clearcarturl;
    }
    public function getMerchantId()
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestPublicKey();
            $url = Constant::URL_CHECKPUBLICKEY_TEST;
        } else {
            $configkey = $this->getLivePublicKey();
            $url = Constant::URL_CHECKPUBLICKEY_LIVE;
        }

        if (empty($configkey)) {
            return false;
        }

        $field = [];
        $type = 'GET';

        $key = base64_encode("{$configkey}".":");
        $ch = curl_init($url);


        $request_headers = array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        );





        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);


        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);
        return $json['merchantId'];
    }
    public function sendMerchantCurl($url, $field, $type)
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestPublicKey();
        } else {
            $configkey = $this->getLivePublicKey();
        }

        if (empty($configkey)) {
            return false;
        }



        $key = base64_encode("{$configkey}".":");
        $ch = curl_init($url);


        $request_headers = array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        );





        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);


        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);
        return json_decode($body, true);
    }


    public function afterpayCheckout($url, $field, $type)
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestPublicKey();
        } else {
            $configkey = $this->getLivePublicKey();
        }

        if (empty($configkey)) {
            return false;
        }

        $ch = curl_init($url);


        $request_headers = array(
            "Cache-Control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
        );


        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);


        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);
        // return json_decode($body, true);
        return $body;
    }
    public function sendPublicCurl($url, $field, $type)
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestPublicKey();
        } else {
            $configkey = $this->getLivePublicKey();
        }

        if (empty($configkey)) {
            return false;
        }



        $key = base64_encode("{$configkey}".":");
        $ch = curl_init($url);


        $request_headers = array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        );





        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);


        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);
        return json_decode($body, true);
    }

    public function sendPrivateCurl($url, $field, $type)
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestSecretKey();
        } else {
            $configkey = $this->getLiveSecretKey();
        }

        if (empty($configkey)) {
            return false;
        }



        $key = base64_encode("{$configkey}".":");
        $ch = curl_init($url);

        $request_headers = array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        );





        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);
        return json_decode($body, true);
    }
    public function sendTokenCurl($url, $field, $type)
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestTokenKey();
        } else {
            $configkey = $this->getLiveTokenKey();
        }

        if (empty($configkey)) {
            return false;
        }

        $ch = curl_init($url);

        $request_headers = array(
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Content-Type: application/x-www-form-urlencoded",
        );


        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".urlencode($configkey));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);
        return json_decode($body, true);
    }


    public function sendAPICurl($url, $field, $type)
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestPublicKey();
        } else {
            $configkey = $this->getLivePublicKey();
        }

        if (empty($configkey)) {
            return false;
        }

        $key = base64_encode("{$configkey}".":");
        $ch = curl_init($url);


        $request_headers = array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        );


        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);
        return json_decode($body, true);
    }

    public function sendCHARGECurl($url, $field, $type)
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestSecretKey();
        } else {
            $configkey = $this->getLiveSecretKey();
        }

        if (empty($configkey)) {
            return false;
        }

        $key = base64_encode("{$configkey}".":");
        $ch = curl_init($url);


        $request_headers = array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        );


        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);
        return json_decode($body, true);
    }

    public function sendQRCurl($url, $field, $type)
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestPublicKey();
        } else {
            $configkey = $this->getLivePublicKey();
        }

        if (empty($configkey)) {
            return false;
        }

        $ch = curl_init($url);


        $request_headers = array(
            "Cache-Control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
        );


        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        if ($body=="Incomplete information") {
          $body = 'error : Incomplete information';
        }else{
          // $body = ob_start();'\n<img src="data:image/png;base64,' . base64_encode($body) . '">';
          $body = 'data:image/png;base64,' . base64_encode($body) . '';
        }




        curl_close($ch);
        return $body;
    }
    public function sendBARCurl($url, $field, $type)
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestPublicKey();
        } else {
            $configkey = $this->getLivePublicKey();
        }

        if (empty($configkey)) {
            return false;
        }

        $ch = curl_init($url);


        $request_headers = array(
            "Cache-Control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
        );


        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        if ($body=="Incomplete information") {
          $body = 'error : Incomplete information';
        }else{
          // $body = ob_start();'\n<img src="data:image/png;base64,' . base64_encode($body) . '">';
          $body = 'data:application/pdf;base64,' . base64_encode($body) . '';
        }




        curl_close($ch);
        return $body;
    }

    
    public function createOtpCharge($accountId, $order)
    {
        try {
            $gbprimepayCardId = $payment->getAdditionalInformation('gbprimepayCardId');
            $order = $payment->getOrder();
            
            $customer_full_name = $order->getBillingAddress()->getFirstname() . ' ' . $order->getBillingAddress()->getLastname();
            $callgetMerchantId = $this->_config->getMerchantId();
            $callgenerateID = $this->_config->generateID();
            $_orderId = $order->getEntityId();
            $_incrementId = $order->getIncrementId();
            $itemamount = number_format((($amount * 100)/100), 2, '.', '');
            $itemdetail = 'Charge for order ' . $_incrementId;
            $itemReferenceId = ''.substr(time(), 4, 5).'00'.$_orderId;
            $itemcustomerEmail = $order->getCustomerEmail();
            $itemcustomerAddress = '' . str_replace("<br/>", " ", $order->getQuote()->getBillingAddress());
            $itemcustomerTelephone = '' . $order->getQuote()->getBillingAddress()->getTelephone();
            $itemmagento_customer_id = $payment->getOrder()->getCustomerId();
            $otpCode = 'Y';
            $otpResponseUrl = $this->_config->getresponseUrl('response_direct');
            $otpBackgroundUrl = $this->_config->getresponseUrl('background_direct');
            
            if ($this->_config->getEnvironment() === 'prelive') {
                $url = Constant::URL_CHARGE_TEST;
            } else {
                $url = Constant::URL_CHARGE_LIVE;
            }
            
            $field = "{\r\n\"amount\": $itemamount,\r\n\"referenceNo\": \"$itemReferenceId\",\r\n\"detail\": \"$itemdetail\",\r\n\"customerName\": \"$customer_full_name\",\r\n\"customerEmail\": \"$itemcustomerEmail\",\r\n\"customerAddress\": \"$itemcustomerAddress\",\r\n\"customerTelephone\": \"$itemcustomerTelephone\",\r\n\"merchantDefined1\": \"$callgenerateID\",\r\n\"merchantDefined2\": null,\r\n\"merchantDefined3\": \"$itemReferenceId\",\r\n\"merchantDefined4\": null,\r\n\"merchantDefined5\": null,\r\n\"card\": {\r\n\"token\": \"$gbprimepayCardId\"\r\n},\r\n\"otp\": \"$otpCode\",\r\n\"responseUrl\": \"$otpResponseUrl\",\r\n\"backgroundUrl\": \"$otpBackgroundUrl\"\r\n}\r\n";
            
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->addDebug("Debug field //" . print_r($field, true));
            }
            
            $callback = $this->_config->sendCHARGECurl("$url", $field, 'POST');
            
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->addDebug("Debug sendCHARGECurl callback //" . print_r($callback, true));
            }
            
            if ($callback['resultCode']=="00") {
                $isLogin = $this->customerSession->isLoggedIn();
                if ($isLogin) {
                    $cardModel = $this->cardFactory->create();
                    $getcardDataSave= $payment->getAdditionalInformation('cardDataSave');
                    if($getcardDataSave){
                        $cardModel->setData($getcardDataSave);
                        $cardModel->save();
                    }
                }
            }
            $getgbprimepay_customer_id= $payment->getAdditionalInformation('gbprimepay_customer_id');
            $item = array(
                "id" => $callgenerateID,
                "tokenreference" => $gbprimepayCardId,
                "resultCode" => $callback['resultCode'],
                "amount" => $itemamount,
                "referenceNo" => $itemReferenceId,
                "detail" => $itemdetail,
                "customerName" => $customer_full_name,
                "customerEmail" => $itemcustomerEmail,
                "customerAddress" => $itemcustomerAddress,
                "customerTelephone" => $itemcustomerTelephone,
                "merchantDefined1" => $callgenerateID,
                "merchantDefined2" => null,
                "merchantDefined3" => $itemReferenceId,
                "merchantDefined4" => null,
                "merchantDefined5" => null,
                "related" => array(
                                "self" => "$getgbprimepay_customer_id",
                                "buyers" => "$callgetMerchantId",
                            ),
                "links" => array(
                                "self" => "/charges/$callgenerateID",
                                "buyers" => "/charges/$callgenerateID/buyers",
                                "sellers" => "/charges/$callgenerateID/sellers",
                                "status" => "/charges/$callgenerateID/status",
                                "fees" => "/charges/$callgenerateID/fees",
                                "transactions" => "/charges/$callgenerateID/transactions",
                                "batch_transactions" => "/charges/$callgenerateID/batch_transactions",
                                ),
            );
            
                        if ($item['tokenreference']) {
                            if ($callback['resultCode'] === '00') {
                                return $item;
                            } else {
                                throw new CouldNotSaveException(
                                    __('Something went wrong. Please try again!')
                                );
                            }
                        } else {
                            throw new CouldNotSaveException(
                                __('Something went wrong. Please try again!')
                            );
                        }
                    } catch (\Exception $exception) {
                        if ($this->_config->getCanDebug()) {
                            $this->gbprimepayLogger->addDebug("cap auth //" . $exception->getMessage());
                        }
            
                        throw new \Exception(
                            $exception->getMessage()
                        );
                    }
    }

    public function getDemoQrcode()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/qrcode-demo.png");
        return $images;
    }
    public function getresponseUrl($routeurl)
    {
        if($routeurl=='success_checkout'){
        $routeurl = $this->_urlBuilder->getUrl("checkout/onepage/success");
        }
        if($routeurl=='cancel_checkout'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/cancel");
        }
        if($routeurl=='failure_checkout'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/failure");
        }
        if($routeurl=='background_checkout'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplacecheckoutorder");
        }
        if($routeurl=='response_direct'){
          $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/pendingdirect");
        }
        if($routeurl=='background_direct'){
          $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplacedirectorder");
        }
        if($routeurl=='response_installment'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/pendinginstallment");
        }
        if($routeurl=='background_installment'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplaceinstallmentorder");
        }
        if($routeurl=='response_qrcode'){
        $routeurl = $this->_urlBuilder->getUrl("checkout/onepage/success");
        }
        if($routeurl=='background_qrcode'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplaceqrcodeorder");
        }
        if($routeurl=='response_qrcredit'){
        $routeurl = $this->_urlBuilder->getUrl("checkout/onepage/success");
        }
        if($routeurl=='background_qrcredit'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplaceqrcreditorder");
        }
        if($routeurl=='response_qrwechat'){
        $routeurl = $this->_urlBuilder->getUrl("checkout/onepage/success");
        }
        if($routeurl=='background_qrwechat'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplaceqrwechatorder");
        }
        if($routeurl=='response_linepay'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/pendinglinepay");
        }
        if($routeurl=='background_linepay'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplacelinepayorder");
        }
        if($routeurl=='response_truewallet'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/pendingtruewallet");
        }
        if($routeurl=='background_truewallet'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplacetruewalletorder");
        }
        if($routeurl=='response_mbanking'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/pendingmbanking");
        }
        if($routeurl=='background_mbanking'){
        $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplacembankingorder");
        }
        if($routeurl=='response_barcode'){
          $routeurl = $this->_urlBuilder->getUrl("checkout/onepage/success");
        }
        if($routeurl=='background_barcode'){
          $routeurl = $this->_urlBuilder->getUrl("gbprimepay/checkout/afterplacebarcodeorder");
        }

        return $routeurl;
    }

    public function getInstructionCheckout()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_checkout/instructions'
        ));
    }


    public function getInstructionDirect()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_direct/instructions'
        ));
    }

    public function getInstructionInstallment()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_installment/instructions'
        ));
    }

    public function getInstructionQrcode()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_qrcode/instructions'
        ));
    }

    public function getInstructionQrcredit()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_qrcredit/instructions'
        ));
    }

    public function getInstructionQrwechat()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_qrwechat/instructions'
        ));
    }

    public function getInstructionLinepay()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_linepay/instructions'
        ));
    }

    public function getInstructionTruewallet()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_truewallet/instructions'
        ));
    }

    public function getInstructionMbanking()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_mbanking/instructions'
        ));
    }

    public function getInstructionBarcode()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_barcode/instructions'
        ));
    }



    public function getTitleDirect()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_direct/title'
        ));
    }
    public function getLogoCheckout()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/gbprimepay-logo-pay.png");
        return $images;
    }
    public function getLogoDirect()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/creditcard.png");
        return $images;
    }
    public function getLogoQrcode()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/qrcode.png");
        return $images;
    }
    public function getLogoQrcredit()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/qrvisa.png");
        return $images;
    }
    public function getLogoQrwechat()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/qrwechat.png");
        return $images;
    }
    public function getLogoLinepay()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/linepay.png");
        return $images;
    }
    public function getLogoTruewallet()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/truewallet.png");
        return $images;
    }
    public function getLogoMbanking()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/mbanking.png");
        return $images;
    }
    public function getLogoBarcode()
    {
        $images = $this->_assetRepo->getUrl("GBPrimePay_Checkout::images/barcode.png");
        return $images;
    }

    public function getTitleCheckout()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_checkout/title'
        ));
    }

    public function getTitleInstallment()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_installment/title'
        ));
    }

    public function getTitleQrcode()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_qrcode/title'
        ));
    }

    public function getTitleQrcredit()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_qrcredit/title'
        ));
    }

    public function getTitleQrwechat()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_qrwechat/title'
        ));
    }

    public function getTitleLinepay()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_linepay/title'
        ));
    }

    public function getTitleTruewallet()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_truewallet/title'
        ));
    }

    public function getTitleMbanking()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_mbanking/title'
        ));
    }

    public function getTitleBarcode()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_barcode/title'
        ));
    }

    public function getTermInstallment($issuer)
    {
        if($issuer=='kasikorn'){
            $issuer = preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
                'gbprimepay/gbprimepay_installment/kasikorn_installment_term'
            ));
        }
        if($issuer=='krungthai'){
            $issuer = preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
                'gbprimepay/gbprimepay_installment/krungthai_installment_term'
            ));
        }
        if($issuer=='thanachart'){
            $issuer = preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
                'gbprimepay/gbprimepay_installment/thanachart_installment_term'
            ));
        }
        if($issuer=='ayudhya'){
            $issuer = preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
                'gbprimepay/gbprimepay_installment/ayudhya_installment_term'
            ));
        }
        if($issuer=='firstchoice'){
            $issuer = preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
                'gbprimepay/gbprimepay_installment/firstchoice_installment_term'
            ));
        }
        if($issuer=='scb'){
            $issuer = preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
                'gbprimepay/gbprimepay_installment/scb_installment_term'
            ));
        }
        if($issuer=='bbl'){
            $issuer = preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
                'gbprimepay/gbprimepay_installment/bbl_installment_term'
            ));
        }
        return $issuer;
    }

    public function getSimpleText()
    {
        return "text";
    }

    public function getLivePublicKey()
    {
        return $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_required/live_public_key'
        );
    }

    public function getLiveSecretKey()
    {
        return $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_required/live_secret_key'
        );
    }

    public function getLiveTokenKey()
    {
        return $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_required/live_token_key'
        );
    }

    public function getTestPublicKey()
    {
        return $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_required/test_public_key'
        );
    }

    public function getTestSecretKey()
    {
        return $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_required/test_secret_key'
        );
    }

    public function getTestTokenKey()
    {
        return $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_required/test_token_key'
        );
    }

    public function getSellerId()
    {
        return $this->_encryptor->decrypt($this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_required/seller_id'
        ));
    }

    public function getCanDebug()
    {
        return $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_required/debug'
        );
    }

    public function getActiveCheckout()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_checkout/active'
        );
    }

    public function getActiveDirect()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_direct/active'
        );
    }

    public function getActiveInstallment()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_installment/active'
        );
    }

    public function getActiveQrcode()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_qrcode/active'
        );
    }

    public function getActiveQrcredit()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_qrcredit/active'
        );
    }

    public function getActiveQrwechat()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_qrwechat/active'
        );
    }

    public function getActiveLinepay()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_linepay/active'
        );
    }

    public function getActiveTruewallet()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_truewallet/active'
        );
    }

    public function getActiveMbanking()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_mbanking/active'
        );
    }

    public function getActiveBarcode()
    {
        return $this->scopeConfig->getValue(
            'payment/gbprimepay_barcode/active'
        );
    }

    public function getEnvironment()
    {
        return $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_required/environment'
        );
    }

    public function getIsSave()
    {
        return "1";
    }

    public function checkActivated()
    {
        return $this->scopeConfig->getValue(
            'gbprimepay/gbprimepay_direct/check_actived'
        );
    }
    
    public function check_term_regex($string,$issuers)
    {
      switch ($issuers) {
        case 'kasikorn':
          $pass_array = array(3, 4, 5, 6, 7, 8, 9, 10);
        break;
        case 'krungthai':
          $pass_array = array(3, 4, 5, 6, 7, 8, 9, 10);
        break;
        case 'thanachart':
          $pass_array = array(3, 4, 6, 10);
        break;
        case 'ayudhya':
          $pass_array = array(3, 4, 6, 9, 10);
        break;
        case 'firstchoice':
          $pass_array = array(3, 4, 6, 9, 10, 12, 18, 24);
        break;
        case 'scb':
          $pass_array = array(3, 4, 6, 10);
        break;
        case 'bbl':
          $pass_array = array(3, 4, 6, 8, 9, 10);
        break;
      }
      $regex = '/^[0-9 ]+(?:,[0-9 ]+)*$/';
      if (preg_match($regex, $string) === 1) {
        $arrterm_check = explode(',',preg_replace('/\s+/', '', $string));
        sort($arrterm_check);

          $arrterm = array();
          foreach($arrterm_check as $key=>$value){
            if (in_array($value, $pass_array)) {
                array_push($arrterm, $value);
            }
          }

      }else{
        $arrterm = array();
      }
      return $arrterm;
    }

    public function obj_term_regex($pass_array,$issuers,$total)
    {
      $objPass = array();
      if(!empty($pass_array)){
      if(($total >= 3000) && (($total/(min($pass_array))) >= 500)){
      switch ($issuers) {
        case 'kasikorn':
            $objPass['kasikorn']['id'] = '004';
            $objPass['kasikorn']['txt'] = 'Kasikornbank Public Company Limited.';
        break;
        case 'krungthai':
            $objPass['krungthai']['id'] = '006';
            $objPass['krungthai']['txt'] = 'Krung Thai Bank Public Company Limited.';
        break;
        case 'thanachart':
            $objPass['thanachart']['id'] = '011';
            $objPass['thanachart']['txt'] = 'TMBThanachart Bank Public Company Limited.';
        break;
        case 'ayudhya':
            $objPass['ayudhya']['id'] = '025';
            $objPass['ayudhya']['txt'] = 'Bank of Ayudhya Public Company Limited.';
        break;
        case 'firstchoice':
            $objPass['firstchoice']['id'] = '026';
            $objPass['firstchoice']['txt'] = 'Krungsri First Choice.';
        break;
        case 'scb':
            $objPass['scb']['id'] = '014';
            $objPass['scb']['txt'] = 'Siam Commercial Bank Public Company Limited.';
        break;
        case 'bbl':
            $objPass['bbl']['id'] = '002';
            $objPass['bbl']['txt'] = 'Bangkok Bank Public Company Limited.';
        break;
      }
      $arrpassterm = array();
      foreach($pass_array as $key=>$value){
        if(($total >= 3000) && (($total/($value)) >= 500)){
          array_push($arrpassterm, $value);
        }
      }
          
          if(!empty($arrpassterm)){
            switch ($issuers) {
                case 'kasikorn':
                    $objPass['kasikorn']['term'] = $arrpassterm;
                break;
                case 'krungthai':
                    $objPass['krungthai']['term'] = $arrpassterm;
                break;
                case 'thanachart':
                    $objPass['thanachart']['term'] = $arrpassterm;
                break;
                case 'ayudhya':
                    $objPass['ayudhya']['term'] = $arrpassterm;
                break;
                case 'firstchoice':
                    $objPass['firstchoice']['term'] = $arrpassterm;
                break;
                case 'scb':
                    $objPass['scb']['term'] = $arrpassterm;
                break;
                case 'bbl':
                    $objPass['bbl']['term'] = $arrpassterm;
                break;
              }
          }
          }
          }
      return $objPass;
    }

    public function gen_term_regex($pass_array,$issuers,$total)
    {
      $echoterm = '';
      if(!empty($pass_array)){
      if(($total >= 3000) && (($total/(min($pass_array))) >= 500)){
      switch ($issuers) {
        case 'kasikorn':
          $echoterm .= '<optgroup label="TextValue[\'Kasikornbank Public Company Limited.\',\'004\']">';
        break;
        case 'krungthai':
          $echoterm .= '<optgroup label="TextValue[\'Krung Thai Bank Public Company Limited.\',\'006\']">';
        break;
        case 'thanachart':
          $echoterm .= '<optgroup label="TextValue[\'TMBThanachart Bank Public Company Limited.\',\'011\']">';
        break;
        case 'ayudhya':
          $echoterm .= '<optgroup label="TextValue[\'Bank of Ayudhya Public Company Limited.\',\'025\']">';
        break;
        case 'firstchoice':
          $echoterm .= '<optgroup label="TextValue[\'Krungsri First Choice.\',\'026\']">';
        break;
        case 'scb':
          $echoterm .= '<optgroup label="TextValue[\'Siam Commercial Bank Public Company Limited.\',\'014\']">';
        break;
        case 'bbl':
          $echoterm .= '<optgroup label="TextValue[\'Bangkok Bank Public Company Limited.\',\'002\']">';
        break;
      }

      foreach($pass_array as $key=>$value){
        if(($total >= 3000) && (($total/($value)) >= 500)){
          $echoterm .= '<option value="' . $value . '">' . $value . ' months</option>';
        }
      }
          $echoterm .= '</optgroup>';
          }
          }
      return $echoterm;
    }
    public function setGBPMethod($value)
    {
        return $this->checkoutSession->setGBPMethod($value);;
    }
    public function getGBPMethod()
    {
        return $this->checkoutSession->getGBPMethod();
    }
    public function setGBPTransactionAMT($value)
    {
        return $this->checkoutSession->setGBPTransactionAMT($value);
    }
    public function getGBPTransactionAMT()
    {
        return $this->checkoutSession->getGBPTransactionAMT();
    }
    public function setGBPTransactionID($value)
    {
        return $this->checkoutSession->setGBPTransactionID($value);
    }
    public function getGBPTransactionID()
    {
        return $this->checkoutSession->getGBPTransactionID();
    }
    public function setGBPTransactionKEY($value)
    {
        return $this->checkoutSession->setGBPTransactionKEY($value);
    }
    public function setGBPTransactionINFO($value)
    {
        return $this->checkoutSession->setGBPTransactionINFO($value);
    }
    public function getGBPTransactionKEY()
    {
        return $this->checkoutSession->getGBPTransactionKEY();
    }
    public function getGBPTransactionINFO()
    {
        return $this->checkoutSession->getGBPTransactionINFO();
    }
    public function setGBPFormKEY($value)
    {
        return $this->checkoutSession->setGBPFormKEY($value);
    }
    public function getGBPFormKEY()
    {
        return $this->_formKey->getFormKey();
    }
    public function setGBPTransactionITEM($value)
    {
        return $this->checkoutSession->setGBPTransactionITEM($value);
    }
    public function getGBPTransactionITEM()
    {
        return $this->checkoutSession->getGBPTransactionITEM();
    }
    public function unsGBPTransactionITEM()
    {
        return $this->checkoutSession->unsGBPTransactionITEM();
    }
    public function unsGBPTransactionKEY()
    {
        return $this->checkoutSession->unsGBPTransactionKEY();
    }
    public function unsGBPTransactionINFO()
    {
        return $this->checkoutSession->unsGBPTransactionINFO();
    }
    public function unsGBPTransactionID()
    {
        return $this->checkoutSession->unsGBPTransactionID();
    }
    public function unsGBPTransactionAMT()
    {
        return $this->checkoutSession->unsGBPTransactionAMT();
    }
    public function convertCountryCodeToIso3($iso2Code)
    {
        /**
         * @var \Magento\Framework\App\ObjectManager $objectManager
         * @var \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $countryCollection = $objectManager->create('\Magento\Directory\Model\ResourceModel\Country\Collection');
        $countryCode = $countryCollection->addCountryCodeFilter($iso2Code)
            ->getFirstItem()
            ->getData();

        return $countryCode['iso3_code'] ? $countryCode['iso3_code'] : false;
    }

}

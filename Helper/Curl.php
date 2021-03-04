<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Helper;

class Curl extends \Magento\Framework\HTTP\Client\Curl
{
    public function delete($uri)
    {
        $this->makeRequest("DELETE", $uri);
    }

    public function patch($uri, $params)
    {
        $this->makeRequest("PATCH", $uri, $params);
    }
}

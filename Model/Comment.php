<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Model;

use Magento\Config\Model\Config\CommentInterface;
use GBPrimePay\Checkout\Helper\ConfigHelper;

class Comment implements CommentInterface
{
protected $_config;

public function __construct(
ConfigHelper $configHelper
) {

    // parent::__construct($context);
    $this->_config = $configHelper;
}
    public function getCommentText($elementValue)  //the method has to be named getCommentText
    {
        //do some calculations here
        //return $elementValue . 'Some string based on the calculations';

        return $this->_config->getImageURLs('logo');
    }
}

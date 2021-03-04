<?php
/**
 * GBPrimePay_Checkout extension
 * @package GBPrimePay_Checkout
 * @copyright Copyright (c) 2020 GBPrimePay Checkout (https://gbprimepay.com/)
 */

namespace GBPrimePay\Checkout\Controller\Customer;

use GBPrimePay\Checkout\Controller\Customer\Card as Card;

class Del extends Card
{
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $result = $this->jsonFactory->create();
            $tokenid = null;
            $id = $this->getRequest()->getParam('id');
            $model = $this->cardFactory->create()->load($id);
            $data = $model->getData();
            $tokenid = $data['tokenid'];

            $this->_gbprimepayInit();

            $out = [];

            try {
                if ($id) {
                    $model->delete();
                    $model->save();
                    $out = [
                        'success' => true
                    ];
                }
            } catch (\Exception $error) {
                $model->delete();
                $model->save();
                $out = [
                    'success' => true
                ];
            }

            $result->setData($out);

            return $result;
        }

        return "er";
    }
}

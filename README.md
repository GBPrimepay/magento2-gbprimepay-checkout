<img src="https://www.globalprimepay.com/dist/images/logo.svg" width="130" />

# GBPrimePay Payment

## Installation

#### Step 1) -  Install GBPrimePay Checkout

##### Using Composer (recommended)

```
composer require gbprimepay/checkout
```

##### Manual Installation  (not recommended)
Install GBPrimePay Checkout for Magento 2
 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/GBPrimePay/Checkout
 * Copy the content from the unzip folder
 * Flush cache

#### Step 2) -  Enable GBPrimePay Checkout
```
php bin/magento module:enable GBPrimePay_Checkout
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy --force
php bin/magento cache:flush
```

#### Step 3) - Config GBPrimePay Checkout
Log into your Magento Admin, then goto  
Stores -> Configuration -> GBPrimePay -> GBPrimePay Checkout Settings
/*
* Copyright © 2020 GBPrimePay Checkout.
*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
        'ko'
    ],function (
        $,
        Component,
        placeOrderAction,
        selectPaymentMethodAction,
        customer,
        checkoutData,
        fullScreenLoader,
        additionalValidators,
        url,
        ko
        ) {
        'use strict';
  
  
            $(window).on('hashchange', function() {
                  var hash = window.location.hash;
                  var selected = $('input[name="payment[method]"]:checked').val();
                  if((hash=="#payment") && (selected=="gbprimepay_checkout")){
					window.console.log("hashchange - done");
                    $('input[name="payment[method]"]:checked').trigger("click");
                  }
            });
  
        return Component.extend({
            defaults: {
                template: 'GBPrimePay_Checkout/payment/gbprimepay_checkout',
                redirectAfterPlaceOrder: false
            },
            initObservable: function () {
                this._super().observe({
                    sayHello: '1',
                });
                var self = this; 
                
                fullScreenLoader.startLoader();
                this.isPlaceOrderActionAllowed(false);
                  
                // this.selectPaymentMethod();
                
                return this;
            },
            getCode: function () {
                return 'gbprimepay_checkout';
            },
            validate: function () {
                return true;
            },
            getInstructionCheckout: function () {
                return window.gbprimepay.instructionCheckout;
            },
            getLogoCheckout: function () {
                return window.gbprimepay.logoCheckout;
            },
            getTitleCheckout: function () {
                return window.gbprimepay.titleCheckout;
            },
            getTransactionID: function () {
                return window.gbprimepay.transaction_id;
            },
            getTransactionKEY: function () {
                return window.gbprimepay.transaction_key;
            },
            getTransactionINFO: function () {
              var transaction_info = $("input[name='transaction_info']").val();
                  return transaction_info;
            },
            getFormKey: function () {
                return window.checkoutConfig.formKey;
            },
            getData: function () {
                var transaction_id = $("input[name='payment[transaction_id]']").val();
                var transaction_form = $("input[name='form_key']").val();
                var increment_id = $("input[name='payment[increment_id]']").val();
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_id': transaction_id,
                        'transaction_form': transaction_form,
                        'increment_id': increment_id,
                    }
                };
            },
            loadCheckoutRender: function () {
                var self = this;                       
                fullScreenLoader.startLoader();
                self.isPlaceOrderActionAllowed(false);
                $.ajax({
                    type: 'POST',
                    url: window.gbprimepay.beforeCheckout,
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            $("input[name='payment[transaction_id]']").val(response.transaction_id);
                            $("input[name='payment[transaction_key]']").val(response.transaction_key);
                            $("input[name='transaction_info']").val(response.transaction_info);
                            $('#GBPCdummy').html(response.transaction_info);
                            $('#GBPCdummy').attr('data-info', response.transaction_info);
							window.console.log("beforeCheckout - done"); 
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                        }
                        if (response.error) {
                            fullScreenLoader.stopLoader();
                            $(".loading-mask").hide();
                            self.messageContainer.addErrorMessage({
                                message: response.message
                            });
                        }
                    },
                    error: function (response) {
                        fullScreenLoader.stopLoader();
                        $(".loading-mask").hide();
                        self.messageContainer.addErrorMessage({
                            message: "Error, please try again"
                        });
                    }
                });
            },
            readyCheckoutRender: function () {
                var self = this;                       
                fullScreenLoader.startLoader();
                self.isPlaceOrderActionAllowed(false);
				var data_submit = {
					'serialID': $("input[name='payment[transaction_key]']").val(),
				};
                $.ajax({
                    type: 'POST',
                    url: window.gbprimepay.renderCheckout,
					data: data_submit,
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
							window.console.log("renderCheckout - done");
                        }
                        if (response.error) {
							window.console.log("renderCheckout - done");
                        }
                    },
                    error: function (response) {
                        if (response) {
							window.console.log("renderCheckout - done");
                        }
                    }
                });
            },
            placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }
  
                var self = this,
                    placeOrder,
                    emailValidationResult = customer.isLoggedIn(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]';
                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }
                if (emailValidationResult && this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);

                    this.getPlaceOrderDeferredObject()
                        .fail(function () {
                            window.console.log("getOrder - fail");   
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                            })
                        .done(function (orderId) {  
                            var $orderId = orderId;
                            fullScreenLoader.startLoader();
                            self.isPlaceOrderActionAllowed(false);
                            window.console.log("getOrder - done");  
                            var placeorderer = setInterval(function () {
                                if ($("input[name='payment[transaction_id]']").length > 0) {
                                    if ($orderId) {
                                        window.console.log("Continue to Payment - done"); 
                                        self.afterPlaceOrder(orderId);
                                    }
                                clearInterval(placeorderer);
                                }
                            }, 700);

                        },
                        );
                    return true;
                }
                return false;
            },
            afterPlaceOrder: function (orderId) {    
            var $orderId = orderId;
            var $orderKey = $("input[name='payment[transaction_key]']").val();     
            var $orderFormkey = $("input[name='form_key']").val();   
            window.console.log("afterPlaceOrder - done");   
            fullScreenLoader.startLoader();
            this.isPlaceOrderActionAllowed(false);
            if ($orderId) {
                
            this.readyCheckoutRender();

            var redirector = setInterval(function () {
                if ($("input[name='payment[transaction_id]']").length > 0) {
                if ($('input[name="payment[method]"]:checked').val() == 'gbprimepay_checkout') {                    
                    window.console.log("redirector - done"); 
                    window.location.replace(url.build('gbprimepay/checkout/redirectcheckout/id/' + $orderId + '/form_key/' + $orderFormkey + '/key/' + $orderKey));                        
                }
                clearInterval(redirector);
                }
            }, 700);
                            
            }
  
            },
            getMessageContainer: function () {
                return this.messageContainer;
            },
            getPlaceOrderDeferredObject: function () {
                return $.when(
                    placeOrderAction(this.getData(),
                        this.getMessageContainer()),
                );
            },
            selectPaymentMethod: function () {
                var result = this._super();
                this.loadCheckoutRender();
                return result;
            },
            loadJsAfterKoRender: function(){
				window.console.log("render - done");  
                this.selectPaymentMethod();
                fullScreenLoader.stopLoader();
                this.isPlaceOrderActionAllowed(true);
                
  
                var generator = setInterval(function () {
                    if ($("input[name='payment[transaction_id]']").length > 0) {
                      var hash = window.location.hash;
                      var selected = $('input[name="payment[method]"]:checked').val();
                      if ((hash == "#payment") && (selected == "gbprimepay_checkout")) {
						  window.console.log("generator - done");
                          $('input[name="payment[method]"]:checked').trigger("click");
                      }
                      clearInterval(generator);
                    }
                }, 700);

            }
        });
    }
  );
  
/*
 * Copyright Extend (c) 2023. All rights reserved.
 * See Extend-COPYING.txt for license details.
 */


    'use strict';

window.cart_utils = (customerData) => {
    return {
        getCartItems: function () {

            if (customerData.cart && customerData.cart.items){
                return customerData.cart.items ;
            }else{
                return [];
            }
        },
        getCartData: function () {
            return customerData.cart;
        },
        refreshMiniCart: function () {
            hyva.setCookie('mage-cache-sessid', '', -1, true); // remove the cookie
            window.dispatchEvent(new CustomEvent("reload-customer-section-data")); // reload the data
        },
        mapToExtendCartItem: function (magentoCartItem) {
            return {
                name: magentoCartItem.product_name,
                sku: magentoCartItem.product_sku,
                qty: magentoCartItem.qty,
                price: magentoCartItem.product_price_value * 100,
                item_id: magentoCartItem.product_id,
                options: [],
            }
        },
    }
}



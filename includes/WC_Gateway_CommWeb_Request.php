<?php

/*

Copyright 2016 Jorge Valdivia

This file is part of Comm-web plugin.

Comm-web plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Comm-web plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Comm-web.  If not, see <http://www.gnu.org/licenses/>.

*/


class WC_Gateway_CommWeb_Request {

    /**
     * Pointer to gateway making the request.
     * @var WC_Gateway_Comm_Web
     */
    protected $gateway;

    /**
     * Endpoint for requests from Comm_Web.
     * @var string
     */
    protected $notify_url;
    protected $log_mode;
    protected $TAG = 'COMM_WEB: ';

    /**
     * Constructor.
     * @param WC_Gateway_Comm_Web $gateway
     */
    function __construct($gateway) {
        $this->gateway    = $gateway;
        $this->notify_url = WC()->api_request_url( 'WC_Gateway_Comm_Web' );
        $this->log_mode = ( $gateway->logs == "yes" ) ? true : false;
    }

    /**
     * Get the CommWeb request URL for an order.
     * @param  WC_Order $order
     * @return string
     */
    public function get_request_url( $order) {
        $order = new WC_Order( $order );
        $orderTotal = $order->get_total() * 100;
        $orderID = $order->id;

        // Get admin options
        $merchantID = $this->gateway->merchant_id;
        $access_code = $this->gateway->access_code;
        $secret_hash = $this->gateway->secret_hash;
        $vpc_ReturnURL = $this->notify_url;

        // Set request URL
        $vpcURL = 'https://migs.mastercard.com.au/vpcpay' . '?';

        $vpc_MerchTxnRef = 'woo-payment';


        $data = array(
            'vpc_Version' => '1',
            'vpc_Command' => 'pay',
            'vpc_AccessCode' => $access_code,
            'vpc_MerchTxnRef' => $vpc_MerchTxnRef,
            'vpc_Merchant' => $merchantID,
            'vpc_OrderInfo' => 'woo-order_'.$orderID,
            'vpc_Amount' => $orderTotal,
            'vpc_ReturnURL' => $vpc_ReturnURL,
            'vpc_Currency' => 'AUD',
            'vpc_Locale' => 'en_AU',
        );

        ksort ($data);

        // set a parameter to show the first pair in the URL
        $appendAmp = 0;
        $secret_hash_value = '';
        foreach($data as $key => $value) {

            // create the input for the SHA256 and URL leaving out any fields that have no value
            if (strlen($value) > 0) {

                // this ensures the first paramter of the URL is preceded by the '?' char
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $secret_hash_value .= $key . '=' . $value;
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                    $secret_hash_value .= '&' . $key . "=" . $value;
                }
            }
        }
        // Create the secure hash and append it to the Virtual Payment Client Data if
        // the merchant secret has been provided.
        if (strlen($secret_hash_value) > 0) {            
            $temp = strtoupper(hash_hmac('SHA256', $secret_hash_value, pack('H*',$this->gateway->secret_hash)));
            $vpcURL .= "&vpc_SecureHash=" . $temp;
            $vpcURL .= "&vpc_SecureHashType=SHA256";
        }
        return $vpcURL;
    }
}
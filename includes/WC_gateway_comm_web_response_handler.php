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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_gateway_comm_web_response_handler {

    protected $SECURE_SECRET;
    protected $gateway;

    protected $TAG = 'COMM_WEB: ';
    protected $log_mode;

    function __construct($gateway) {
        $this->gateway    = $gateway;
        $this->SECURE_SECRET = $gateway->secret_hash;
        $this->log_mode = ( $gateway->logs == "yes" ) ? true : false;
        add_action( 'woocommerce_api_wc_gateway_comm_web', array( $this, 'check_response' ) );
        add_action( 'valid-comm-web-response', array( $this, 'valid_response' ) );
    }

    public function check_response() {
        // get and remove the vpc_TxnResponseCode code from the response fields as we
        // do not want to include this field in the hash calculation
        $vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
        unset($_GET["vpc_SecureHash"]);

        if (strlen($this->SECURE_SECRET) > 0 && $_GET["vpc_TxnResponseCode"] != "7"
            && $_GET["vpc_TxnResponseCode"] != "No Value Returned") {

            $md5HashData = $this->SECURE_SECRET;
            //if ($this->log_mode) { error_log($this->TAG . "SECURE SECRET = " . $this->SECURE_SECRET); }

            // sort all the incoming vpc response fields and leave out any with no value
            foreach($_GET as $key => $value) {
                if ($key != "vpc_Secure_Hash" or strlen($value) > 0) {
                    $md5HashData .= $value;
                }
            }
            // Validate the Secure Hash (remember MD5 hashes are not case sensitive)
            // This is just one way of displaying the result of checking the hash.
            // In production, you would work out your own way of presenting the result.
            // The hash check is all about detecting if the data has changed in transit.
            if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData))) {
                if ( $this->log_mode ) { error_log($this->TAG . "VALID HASH"); }
                do_action( 'valid-comm-web-response', $_GET);
                exit;
            } else {
                if ( $this->log_mode ) { error_log($this->TAG . "NOT VALID HASH"); }
            }
        } else {
            if ( $this->log_mode ) { error_log($this->TAG . "HASH NOT CALCULATED (FIELD EMPTY?)"); }
        }
        wp_die( 'Payment Request Failure', 'Comm Web Request', array( 'response' => 500 ) );
    }
    public function valid_response($response) {
        global $woocommerce;
        $raw_order = $response['vpc_OrderInfo'];
        $order = $this->getOrder($raw_order);
        if ($this->log_mode ) { error_log($this->TAG . 'Order Found: ' . $order->id); }

        $responseCode = $response['vpc_TxnResponseCode'];
        if ($responseCode == '0' && $response['vpc_Amount'] == $order->get_total() * 100) {
            if ($this->log_mode ) { error_log($this->TAG . 'Payment Completed'); }
            $order->add_order_note( 'CommWeb TransactionNo: '. $response['vpc_TransactionNo']);
            $order->add_order_note( 'Payment completed' );
            $order->payment_complete();
            // Empty the cart (Very important step)
            $woocommerce->cart->empty_cart();
            wp_redirect($this->gateway->get_return_url( $order ) );
            exit;
        }else{
            // Transaction was not successful
            if ($this->log_mode ) { error_log($this->TAG . 'Transaction was not successful'); }
            if ($this->log_mode ) { error_log($this->TAG . 'Bank Response: '. $this->getResponseDescription($responseCode)); }
            // Add notice to the cart
            wc_add_notice( 'Sorry your payment can not be completed. Please try again. '.$this->getResponseDescription($responseCode), 'error' );
            // Add note to the order for your reference
            $order->add_order_note( 'Payment not completed. CommWeb response: '. $this->getResponseDescription($responseCode) );
            wp_redirect(wc_get_checkout_url());
            exit;
        }
    }

    function getOrder($raw_order){
        $order_id = explode('_', $raw_order)[1];
        if ( empty( $order_id) ) {
            if ($this->log_mode ) { error_log($this->TAG . "OrderID in response is empty"); }
            return;
        }
        if ( ! $order = wc_get_order( $order_id ) ) {
            if ($this->log_mode ) { error_log($this->TAG . "Order can not be retrieved from WooCoomerce. OrderID: ". $order_id); }
            return;
        }
        return $order;
    }

    function getResponseDescription($responseCode) {

        switch ($responseCode) {
            case "0" : $result = "Transaction Successful"; break;
            case "?" : $result = "Transaction status is unknown"; break;
            case "1" : $result = "Unknown Error"; break;
            case "2" : $result = "Bank Declined Transaction"; break;
            case "3" : $result = "No Reply from Bank"; break;
            case "4" : $result = "Expired Card"; break;
            case "5" : $result = "Insufficient funds"; break;
            case "6" : $result = "Error Communicating with Bank"; break;
            case "7" : $result = "Payment Server System Error"; break;
            case "8" : $result = "Transaction Type Not Supported"; break;
            case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
            case "A" : $result = "Transaction Aborted"; break;
            case "C" : $result = "Transaction Cancelled"; break;
            case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
            case "F" : $result = "3D Secure Authentication failed"; break;
            case "I" : $result = "Card Security Code verification failed"; break;
            case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
            case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
            case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
            case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
            case "S" : $result = "Duplicate SessionID (OrderInfo)"; break;
            case "T" : $result = "Address Verification Failed"; break;
            case "U" : $result = "Card Security Code Failed"; break;
            case "V" : $result = "Address Verification and Card Security Code Failed"; break;
            default  : $result = "Unable to be determined";
        }
        return $result;
    }
}
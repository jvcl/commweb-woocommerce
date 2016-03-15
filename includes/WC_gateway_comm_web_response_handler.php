<?php

/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 21/02/2016
 * Time: 2:41 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include_once( 'WC_gateway_comm_web_response.php' );


class WC_gateway_comm_web_response_handler extends WC_gateway_comm_web_response{

    protected $SECURE_SECRET;

    protected $TAG = 'COMM_WEB: ';

    function __construct($SECURE_SECRET) {

        $this->SECURE_SECRET = $SECURE_SECRET;
        add_action( 'woocommerce_api_wc_gateway_comm_web', array( $this, 'check_response' ) );
        add_action( 'valid-comm-web-response', array( $this, 'valid_response' ) );
    }

    public function check_response() {
        // get and remove the vpc_TxnResponseCode code from the response fields as we
        // do not want to include this field in the hash calculation
        $vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
        unset($_GET["vpc_SecureHash"]);

        // set a flag to indicate if hash has been validated
        $errorExists = false;

        if (strlen($this->SECURE_SECRET) > 0 && $_GET["vpc_TxnResponseCode"] != "7"
            && $_GET["vpc_TxnResponseCode"] != "No Value Returned") {

            $md5HashData = $this->SECURE_SECRET;
            error_log("SECURE SECRET = " . $this->SECURE_SECRET);

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
                error_log($this->TAG . "VALID HASH");
                do_action( 'valid-comm-web-response', $_GET);
                exit;
            } else {
                error_log("$this->TAG .NOT VALID HASH");
            }
        } else {
            error_log($this->TAG . "HASH NOT CALCULATED (FIELD EMPTY?)");
        }
        wp_die( 'Payment Request Failure', 'Comm Web Request', array( 'response' => 500 ) );
    }
    public function valid_response($response) {

    }
}
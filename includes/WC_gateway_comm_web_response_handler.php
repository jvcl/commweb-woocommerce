<?php

/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 21/02/2016
 * Time: 2:41 PM
 */
class WC_gateway_comm_web_response_handler {
    function __construct() {
        add_action( 'woocommerce_api_wc_gateway_comm_web', array( $this, 'check_response' ) );
        add_action( 'valid-comm-web-response', array( $this, 'valid_response' ) );
    }

    public function check_response() {
        if ( ! empty( $_POST )  ) {
            $posted = wp_unslash( $_POST );

            do_action( 'valid-comm-web-response', $posted );
            exit;
        }

        wp_die( 'Comm Web Request Failure', 'Comm Web Request', array( 'response' => 500 ) );
    }

    public function valid_response(){

    }
}
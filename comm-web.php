<?php

/*
Plugin Name: WooCommerce CommWeb Virtual Payment
Plugin URI: http://wooplugins.com.au
Description: WooCommerce CommWeb Virtual Payment
Version: 1.0
Author: WooPlugins - Jorge Valdivia
Author URI: http://wooplugins.com.au
License: GNU GPLv3

*/


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

function flyonet_comm_web() {

    if (!class_exists('WC_Payment_Gateway'))  return;

    class WC_Gateway_Comm_Web extends WC_Payment_Gateway {

        protected $TAG = 'COMM_WEB: ';

        function __construct() {
            // The global ID for this Payment method
            $this->id = "comm_web";
            // The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
            $this->method_title = __( "CommWeb Virtual Payment ", 'comm_web' );
            // The description for this Payment Gateway, shown on the actual Payment options page on the backend
            $this->method_description = __( "CommWeb Virtual PaymentPay. Cards accepted: Visa, Mastercard & MasterPass", 'comm_web' );
            // The title to be used for the vertical tabs that can be ordered top to bottom
            $this->title = __( 'CommWeb Virtual Payment', 'comm_web' );
            // If you want to show an image next to the gateway's name on the frontend, enter a URL to an image.
            $this->icon = WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__)) . '/images/visa-master.png';;
            // Bool. Can be set to true if you want payment fields to show on the checkout
            // if doing a direct integration, which we are doing in this case
            $this->has_fields = false;
            // This basically defines your settings which are then loaded with init_settings()
            $this->init_form_fields();
            // After init_settings() is called, you can get the settings and load them into variables
            $this->init_settings();

            // Turn these settings into variables we can use
            foreach ( $this->settings as $setting_key => $value ) {
                $this->$setting_key = $value;
            }

            // Save admin options
            add_action( 'woocommerce_update_options_payment_gateways_' .
                $this->id, array( $this, 'process_admin_options' ) );

            // Add call back handler
            include_once( 'includes/WC_gateway_comm_web_response_handler.php' );
            new WC_gateway_comm_web_response_handler($this);
        }

        public function init_form_fields() {
            $this->form_fields = include( 'includes/settings_comm_web.php' );
        }

        // Submit payment and handle response
        public function process_payment( $order_id ) {
            include_once('includes/WC_Gateway_CommWeb_Request.php');
            $log_mode = ( $this->logs == "yes" ) ? true : false;
            $comm_web_request = new WC_Gateway_CommWeb_Request($this);
            $order = new WC_Order( $order_id );
            $vpcURL = $comm_web_request->get_request_url($order);

            if ( $log_mode ) { error_log($this->TAG . "Sending request to url: " . $vpcURL); }

            // Redirect to commweb page
            return array(
                'result'   => 'success',
                'redirect' => "$vpcURL",
            );
        }
    }
}

add_action('plugins_loaded', 'flyonet_comm_web');

function flyonet_add_comm_gateway_class( $methods ) {
    $methods[] = 'WC_Gateway_Comm_Web';
    return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'flyonet_add_comm_gateway_class' );
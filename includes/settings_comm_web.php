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

return array(
    'enabled' => array(
        'title' => __( 'Enable/Disable', 'comm_web' ),
        'type' => 'checkbox',
        'label' => __( 'Enable CommWeb payments', 'comm_web' ),
        'default' => 'no'
    ),
    'title' => array(
        'title' => __( 'Title', 'comm_web' ),
        'type' => 'text',
        'description' => __( 'This controls the title which the user sees during checkout.', 'comm_web' ),
        'default' => __( 'Credit Card Payment', 'comm_web' ),
        'desc_tip'      => true,
    ),
    'description' => array(
        'title'		=> __( 'Description', 'comm_web' ),
        'type'		=> 'textarea',
        'desc_tip'	=> __( 'Payment description the customer will see during the checkout process.', 'comm_web' ),
        'default'	=> __( 'Pay securely using your credit card. Cards accepted: Visa, Mastercard & MasterPass', 'comm_web' ),
        'css'		=> 'max-width:350px;'
    ),
    'merchant_id' => array(
        'title'     => __( 'Merchant ID', 'comm_web' ),
        'type'      => 'textbox',
        'description' => __( 'Enter the Merchant ID provided by the bank.', 'comm_web' ),
    ),
    'access_code' => array(
        'title'     => __( 'Access Code', 'comm_web' ),
        'type'      => 'text',
        'description' => __( 'Enter the Access Code provided by the bank.', 'comm_web' ),
    ),
    'secret_hash' => array(
        'title'     => __( 'Secret Hash Secret', 'comm_web' ),
        'type'      => 'text',
        'description' => __( 'Enter the Secret Hash Secret provided by the bank.', 'comm_web' ),
        'default' => __( '', 'comm_web' ),
    ),
    'logs' => array(
        'title' => __( 'Enable Logs', 'comm_web' ),
        'type' => 'checkbox',
        'label' => __( 'Enable logs.', 'comm_web' ),
        'desc_tip'	=> __( "Enable to see the plugin logs in your php log file", 'comm_web' ),
        'default' => 'no'
    ),
);
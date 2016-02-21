<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 21/02/2016
 * Time: 1:41 PM
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
        'default'	=> __( 'Pay securely using your credit card.', 'comm_web' ),
        'css'		=> 'max-width:350px;'
    ),
    'merchant_id' => array(
        'title'     => __( 'Merchant ID', 'comm_web' ),
        'type'      => 'textbox',
        'description' => __( 'Enter the Merchant ID provided by the bank.', 'comm_web' ),
    ),
    'access_code' => array(
        'title'     => __( 'Acess Code', 'comm_web' ),
        'type'      => 'text',
        'description' => __( 'Enter the Access Code provided by the bank.', 'comm_web' ),
    ),
    'secret_hash' => array(
        'title'     => __( 'Secret Hash Secret', 'comm_web' ),
        'type'      => 'text',
        'description' => __( 'Enter the Secret Hash Secret provided by the bank.', 'comm_web' ),
    ),
    'environment' => array(
        'title'     => __( 'Test Mode', 'comm_web' ),
        'label'     => __( 'Enable Test Mode', 'comm_web' ),
        'type'      => 'checkbox',
        'description' => __( 'Place the payment gateway in test mode.', 'comm_web' ),
        'desc_tip'	=> __( 'All transactions will be in test mode.', 'comm_web' ),
        'default'   => 'no',
    ),
    'logs' => array(
        'title' => __( 'Enable Logs', 'comm_web' ),
        'type' => 'checkbox',
        'label' => __( 'Enable logs.', 'comm_web' ),
        'desc_tip'	=> __( "Enable to see the plugin logs in your php log file", 'comm_web' ),
        'default' => 'no'
    ),
);
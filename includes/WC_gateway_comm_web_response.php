<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 15/03/2016
 * Time: 10:35 PM
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_gateway_comm_web_response {

    protected $TAG = 'COMM_WEB: ';

    protected function getOrder($raw_order){
        $order_id = explode('_', $raw_order)[1];
        if ( empty( $order_id) ) {
            error_log($this->TAG . "OrderID in response is empty");
            return;
        }
        if ( ! $order = wc_get_order( $order_id ) ) {
            error_log($this->TAG . "Order can not be retrived from WooCoomerce. OrderID: ". $order_id);
            return;
        }
        return $order;
    }
}
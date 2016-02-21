<?php

/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 21/02/2016
 * Time: 1:24 PM
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

    /**
     * Constructor.
     * @param WC_Gateway_Comm_Web $gateway
     */
    function __construct($gateway) {
        $this->gateway    = $gateway;
        $this->notify_url = WC()->api_request_url( 'WC_Gateway_Comm_Web' );
        error_log("notify " . $this->notify_url);
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

        // Create a unique id for the transaction
//        $xmlID = md5(uniqid(rand(), true));

        // Get admin options
        $test_mode = ( $this->gateway->environment == "yes" ) ? true : false;
        $merchantID = $this->gateway->merchant_id;
        $access_code = $this->gateway->access_code;
        $md5HashData = $this->gateway->secret_hash;
        $vpc_ReturnURL = $this->notify_url;

        $log_mode = ( $this->gateway->logs == "yes" ) ? true : false;

        // Set request URL
        $vpcURL = 'https://migs.mastercard.com.au/vpcpay' . '?';

        // TODO add the values for
        $vpc_MerchTxnRef = 'test';


        $data = array(
            'vpc_Version' => '1',
            'vpc_Command' => 'pay',
            'vpc_AccessCode' => $access_code,
            'vpc_MerchTxnRef' => $vpc_MerchTxnRef,
            'vpc_Merchant' => $merchantID,
            'vpc_OrderInfo' => 'woo_order_'.$orderID,
            'vpc_Amount' => $orderTotal,
            'vpc_ReturnURL' => $vpc_ReturnURL,
            'vpc_Locale' => 'en'
        );

        ksort ($data);

        // set a parameter to show the first pair in the URL
        $appendAmp = 0;

        foreach($data as $key => $value) {

            // create the md5 input and URL leaving out any fields that have no value
            if (strlen($value) > 0) {

                // this ensures the first paramter of the URL is preceded by the '?' char
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
                $md5HashData .= $value;
            }
        }

        // Create the secure hash and append it to the Virtual Payment Client Data if
        // the merchant secret has been provided.
        if (strlen($md5HashData) > 0) {
            $vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($md5HashData));
        }
        return $vpcURL;
    }
}
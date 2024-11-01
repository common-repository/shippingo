<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

add_action( 'wp_ajax_shippingo_register', 'shippingo_register' );
function shippingo_register() {

    $nonce = '';

    if (isset($_POST['nonce'])) {
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
    }

    if (  !wp_verify_nonce($nonce, 'shippingo_nonce') ) {
        wp_send_json_error('Invalid nonce.');
        wp_die();
    }
    
    $responseObject['success'] = 0;   

    $url = shippingo_REST_API.'/w_register';

    $data = [
        'firstname'=>(isset($_POST['shippingo_firstname'])) ? sanitize_text_field(wp_unslash($_POST['shippingo_firstname'])) : '',
        'lastname'=>(isset($_POST['shippingo_lastname'])) ?sanitize_text_field(wp_unslash($_POST['shippingo_lastname'])) : '',
        'company'=>(isset($_POST['shippingo_company'])) ?sanitize_text_field(wp_unslash($_POST['shippingo_company'])) : '',
        'vat'=>(isset($_POST['shippingo_vat_number'])) ?sanitize_text_field(wp_unslash($_POST['shippingo_vat_number'])) : '',
        'email'=>(isset($_POST['shippingo_email'])) ?sanitize_text_field(wp_unslash($_POST['shippingo_email'])) : '',
        'phone'=>(isset($_POST['shippingo_phone'])) ?sanitize_text_field(wp_unslash($_POST['shippingo_phone'])) : '',
        'shippingo'=>sanitize_text_field(wp_unslash(shippingo_ID)),
    ];

    $result = wp_remote_post($url,
        array(
            "headers" => [
                'Content-Type' => 'text/xml',
            ],
            "body" => wp_json_encode($data),
            "sslverify" => false,
        )
    );
    if(!is_wp_error($result)){

        $response = json_decode($result['http_response']->get_response_object()->body);


        if($response->success){

            $responseObject['success'] = 1;
            $responseObject['token'] = $response->token;

            update_option('shippingo_token',$response->token);


            
        } else {

            $responseObject['msg'] = esc_html( __('Please contact plugin support', 'shippingo') );

        }


    }

    

    wp_send_json($responseObject);
    die;

}


add_action( 'wp_ajax_shippingo_validate_key', 'shippingo_validate_key' );
function shippingo_validate_key() {

    $nonce = '';

    if (isset($_POST['nonce'])) {
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
    }

    if (  !wp_verify_nonce($nonce, 'shippingo_nonce') ) {
        wp_send_json_error('Invalid nonce.');
        wp_die();
    }

    $responseObject['success'] = 0;

    if (isset($_POST['token'])) {        

        $token = sanitize_text_field(wp_unslash($_POST['token']));

        update_option('shippingo_token',$token);
        

        if ($token!='') {     

            $url = shippingo_REST_API.'/w_validate_key';

            $data = [];
            $data['token']   = $token;
            $data['shippingo'] = shippingo_ID;
            if($data['token'] != ''){
                $result = wp_remote_post($url,
                    array(
                        "headers" => [
                            'Content-Type' => 'text/xml',
                        ],
                        "body" => wp_json_encode($data),
                        "sslverify" => false,
                    )
                );

                //var_dump($result);
                if(!is_wp_error($result)){

                    $response = json_decode($result['http_response']->get_response_object()->body);


                    if (isset($response->payment_link)) {
                        $responseObject['payment_link'] = $response->payment_link;
                    }

                    if($response->success){


                        $responseObject['success'] = 1;
                        if (isset($response->products)) {
                            $responseObject['products'] = $response->products;
                        }                   

                        
                    }


                }
            }
        }

    }

    wp_send_json($responseObject);
    die;

}



add_action( 'wp_ajax_shippingo_add_order', 'shippingo_add_order' );

function shippingo_add_order() {

    $nonce = '';

    if (isset($_POST['nonce'])) {
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
    }

    if (  !wp_verify_nonce($nonce, 'shippingo_nonce') ) {
        wp_send_json_error('Invalid nonce.');
        wp_die();
    }

    $responseObject['success'] = 0;

    if (isset($_POST['order_id'])) {    

        $token = get_option('shippingo_token','');
        $order_id = sanitize_text_field(wp_unslash($_POST['order_id'])); 

        // NEW2024
        $orders = explode(",",$order_id);        

        if ($token!='') {
            
            foreach($orders as $order_id) {            
            
                $added = shippingo_add_shipping($order_id);
                if ($added['success']==1) {
                    $responseObject['success'] = 1;
                }

            }
        }

    }

    wp_send_json($responseObject);
    die;

}

function shippingo_add_shipping($order_id = 0, $govina = '', $comment = '',$auto=''){


    $url                    = shippingo_REST_API.'/shippingo_add_orders';
    $data                   = array();
    $order                  = wc_get_order( $order_id );
    $order_data             = $order->get_data();


    $shipping_lines = array();
    foreach ($order->get_items('shipping') as $item_id => $shipping_item) {
        $shipping_lines[] = $shipping_item->get_data();
    }



    $order_data['shipping_lines'] = $shipping_lines;


    $order_data['line_items'] = [];
    foreach($order->get_items() as $key=>$item) {

        $item_data = $item->get_data();

        if (!isset($item_data['sku']) || empty($item_data['sku'])) {
            $product = $item->get_product();
            $item_data['sku'] = $product ? $product->get_sku() : '';
        }

        $order_data['line_items'][$item->get_id()] = $item_data;

        if (isset($item['meta_data']) && count($item['meta_data'])>0) {

            $order_data['line_items'][$item->get_id()]['meta_data'] = [];

            foreach($item['meta_data'] as $meta) {

                $order_data['line_items'][$item->get_id()]['meta_data'][] = $meta->get_data();

            }

        }
    }


    $data['order'] = $order_data;
    $data['platform_id'] = 4;
    $data['token'] = get_option('shippingo_token', '');

      
    $result = 0;

    $result = wp_remote_post($url,
        array(
            "headers" => [
                'Content-Type' => 'text/xml',
            ],
            "body" => wp_json_encode($data),
            "sslverify" => false,
            'timeout'    => 30, 
        )
    );

    $result_data = [];

    if(!is_wp_error($result))
    {

        $result_array = json_decode($result['http_response']->get_response_object()->body,true);  
        
        
        
        if($result_array['success'])
        {
            $result_data['success'] = 1;

        }       


    } else {
        
    }
    
    return $result_array;
}


add_action('woocommerce_order_status_changed', 'shippingo_order_status_change', 10, 3);
function shippingo_order_status_change($order_id,$old_status,$new_status) {


    if(!in_array($new_status,['failed','cancelled'])){
        shippingo_add_shipping($order_id);
    }
}

?>
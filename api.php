<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
//https://domain/wp-json/shippingo-17/v1/update-order
function shippingo_register_api_routes() {
    register_rest_route('shippingo-'.shippingo_ID.'/v1', '/update-order/', array(
        'methods'  => 'POST',
        'callback' => 'shippingo_update_order_status',
        'permission_callback' => 'shippingo_permission_check',
    ));

    register_rest_route('shippingo-' . shippingo_ID . '/v1', '/update-token/', array(
        'methods'  => 'POST',
        'callback' => 'shippingo_update_token',
    ));
}
add_action('rest_api_init', 'shippingo_register_api_routes');


function shippingo_update_token(WP_REST_Request $request) {
    $token = $request->get_param('token');
    
    if (empty($token)) {
        return new WP_Error('no_token', 'Token parameter is missing', array('status' => 400));
    }

    update_option('shippingo_token', sanitize_text_field($token));

    return new WP_REST_Response(array(
        'success' => true,
        'message' => 'Token updated successfully',
    ), 200);
}


function shippingo_update_order_status(WP_REST_Request $request) {
    $order_id = $request->get_param('order_id');
    $new_status = $request->get_param('status');
    $token = $request->get_param('token');


    // Validate token
    if (!shippingo_plugin_validate_token($token)) {
        return new WP_Error('invalid_token', 'Invalid token', array('status' => 403));
    }

    // Validate order ID
    $order = wc_get_order($order_id);
    if (!$order) {
        return new WP_Error('invalid_order', 'Invalid order ID', array('status' => 404));
    }

    $shipment = $request->get_param('shipment');

    update_post_meta( $order_id, 'shippingo_shipment', $shipment);

    if ($new_status!='') {
        // Update order status
        $order->update_status($new_status, 'Order status updated via REST API');

        return new WP_REST_Response(wp_json_encode(array(
            'message' => 'Order status updated successfully',
            'order_id' => $order_id,
            'new_status' => $new_status,
            'success'=>1
        )), 200);

    } else {
        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'updated successfully',
        ), 200);
    }
}

function shippingo_plugin_validate_token($token) {
    // Replace with your actual token

    $valid_token = get_option("shippingo_token",'');

    return $token === $valid_token;
}


function shippingo_permission_check(WP_REST_Request $request) {
    
    $token = $request->get_param('token');

    // Validate token
    if (shippingo_plugin_validate_token($token)) {
        return true;
    }

    return new WP_Error('invalid_token', 'Invalid token', array('status' => 403 ));
}


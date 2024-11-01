<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
$shippingo_woo_page = '';
$shippingo_woo_type = 1;

if (isset($_SERVER['REQUEST_URI'])) {
    $uri = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));
}


if(isset($uri) && (strpos( $uri, 'post.php') || strpos( $uri, 'edit.php') || strpos( $uri, 'admin.php'))) {

    if (isset($_GET)) {

        $id = '';
        if (isset($post)) {
            $id = $post->ID;
        }



        if (isset($_GET['post'])) {
            $id = sanitize_text_field(wp_unslash($_GET['post']));
        }

        if (isset($_GET['page']) && $_GET['page']=='wc-orders') {

            $shippingo_woo_type = 2;
            $shippingo_woo_page = 'orders';

            if (isset($_GET['action']) && $_GET['action']=='edit') {
                $shippingo_woo_page = 'edit';
                
            }
        } else if (isset($_GET['post_type']) && $_GET['post_type']=='shop_order') {

            $shippingo_woo_type = 1;
            $shippingo_woo_page = 'orders';

        }

        if (get_post_type($id) == "shop_order") {
            
            $shippingo_woo_page = 'edit';
            
        }

        add_action('admin_enqueue_scripts', 'shippingo_my_script_enqueue');

    }

}



//Load Scripts only on order pages!
function shippingo_my_script_enqueue() {

    global $shippingo_woo_page;

    $nonce = wp_create_nonce('shippingo_nonce');

    $path = 'admin.php?page=shippingo-settings';

    $settings_url = admin_url($path);    

    $settings = array();
    $settings['user_id'] = get_current_user_id();
    $token = get_option('shippingo_token', '');

    $object_data = array(
        "print_link"=>shippingo_PRINT_URL,
        "token" => $token, 
        'ajax_url' => admin_url( 'admin-ajax.php' ), 
        "submit" => esc_html(__('Submit','shippingo')), 
        "cancel" => esc_html(__('Cancel', 'shippingo')),
        "iframe_url"=> shippingo_IFRAME,
        'nonce'   => $nonce,
    );

    if($shippingo_woo_page == 'edit'){

        if (isset($_GET['post'])) {
            $post_data = sanitize_text_field(wp_unslash($_GET['post']));
        }

        if (isset($_GET['id'])){
            $id = sanitize_text_field(wp_unslash($_GET['id']));
        }

        $id = (isset($post_data) && $post_data!='') ? $post_data : $id;

        $order = new WC_Order( $id );

        $object_data['order_data'] = $order->get_data();

    }    
    
    wp_enqueue_style('shippingo-plugin-style', plugins_url( '/css/style_admin.css', __FILE__ ), [],shippingo_version );
    wp_enqueue_script( 'shippingo-scripts',  plugins_url( '/js/scripts.js', __FILE__ ) , [],shippingo_version );
    wp_localize_script( 'shippingo-scripts', 'shippingo_data', $object_data);

    
}

//admin_print_scripts-edit.php



if (in_array($shippingo_woo_page,['edit','orders'])) {
   
    add_filter('admin_footer','shippingo_add_filter_shipping');

    function shippingo_add_filter_shipping() {

        ?>

        <div class="shippingo-dso-modal shippingo-iframe">
            <div class="shippingo-dso-bg"></div>
            <div class="shippingo-dso-con">                              
                <div class="shippingo-dso-con-box">                     
                    <div class="shippingo-dso-con-box-close"></div>  
                    <iframe src="" class="shippingo-iframe"></iframe>
                </div>
            </div>
        </div>       

        <?php
        
    }

}




function shippingo_check_allowed_shipping($order_id){

    $passed = true;
    $order = wc_get_order($order_id);

    $shipping_method_title =  $order->get_items( 'shipping' );
    foreach($shipping_method_title as $v) {
        $passed = $v['method_title'];
    }
    
    return $passed;

}






add_action( 'add_meta_boxes', 'shippingo_add_meta_boxes' );
if ( ! function_exists( 'shippingo_add_meta_boxes' ) )
{
    function shippingo_add_meta_boxes()
    {      
        global $post,$theorder, $shippingo_woo_page; 
        

        $id = '';
        if (is_object($post)) {
            $id = $post->ID;

        } else  if (is_object($theorder)) {
            $id = $theorder->get_id();

        } else if (isset($_GET['id'])){
            $id = sanitize_text_field(wp_unslash($_GET['id']));

        } elseif (isset($_GET['post'])){
            $id = sanitize_text_field(wp_unslash($_GET['post']));
        } 

        //var_dump('$shippingo_woo_page',$shippingo_woo_page,$id);
        
               
        if($shippingo_woo_page == 'edit') {
            

            $shipping_method = shippingo_check_allowed_shipping($id);
            if ($shipping_method == false) {
                return;
            }
            if ($shipping_method==1) {
                $shipping_method = '';
            }

            $screen = 'shop_order';

            if (function_exists('wc_get_page_screen_id')) {
                //$screen = wc_get_page_screen_id( 'shop-order' );
            } else {
                //$screen = 'shop_order';
            }
            add_meta_box('shippingo_meta_fields', esc_html(__('ShippinGo Ecommerce Delivery', 'shippingo'))  . ' ' . esc_html(__('ShippinGo', 'shippingo')) , 'shippingo_fields', $screen , 'side', 'core');

            
            

        }
    }
}
if ( ! function_exists( 'shippingo_fields' ) )
{


    function shippingo_fields($title='',$post_data='')
    {
        

        global $post,$theorder;

        $order_data = '';

        
        
        if (is_object($theorder) && $theorder!='') {            
            $order_data = new stdClass;
            $order_data->ID = $theorder->get_id();        
        }

        if (is_object($post) && $post!='') {            
            $order_data = new stdClass;
            $order_data->ID = $post->ID;        
        }


        if ($post_data!='' && !is_array($post_data) ) {
            $order_data = $post_data;
        }

       
       

        $shipping_method = shippingo_check_allowed_shipping($order_data->ID);
        if ($shipping_method==false) {
            return;
        } 

  
        $shippingo_shipping = get_post_meta( $order_data->ID, 'shippingo_shipment', true);
                
        if ($shippingo_shipping=='') {

            $shippingo_shipping = [];

        } else {            

            $shippingo_shipping = json_decode($shippingo_shipping,true);

        }


        $order = wc_get_order( $order_data->ID );
        $order = $order->get_data();       

        

        ?>   
        <div class="shippingo-shipping-box shipped" data-order-id="<?php echo esc_html($order_data->ID)?>">  
            <div style="display:none" class="shippingo_loader"></div>
            
            <div class="dsb-logo">
                <img src="<?php echo esc_html(plugins_url( '/images/logos/0.png', __FILE__ ));?>">
            </div>
            
            <div class="dsb-con">
            
                <div class="shippingo-shipping-exist">
                <?php
                //Echo shipping number if exists
                if (empty($shippingo_shipping)) {
                    

                    ?>
                    <button class="shippingo_submit submit shippingo_open_iframe" type="button"><?php echo esc_html(__('Submit', 'shippingo'));?></button>
                    <?php


                } else {

                    $shipment = $shippingo_shipping;

 
                    $tracking_number    = $shipment['tracking_number'];  
                    
                    if (isset($shipment['location_name'])) {
                        $location_name = $shipment['location_name'];
                    } else {
                        $location_name = '';
                    }  

                    if (!isset($shipment['platform_id'])) {
                        $shipment['platform_id'] = '';
                    }                                       
                    
                    $shipment['label_url'] = shippingo_PRINT_URL.''.$order_data->ID;
                    $label_url = $shipment['label_url'];
                    
                    ?>
                    <div class="shippingo-shipping-exist-box" data-order-id="<?php echo esc_html($order_data->ID)?>">
                        <p class="shippingo-shipping-message">
                            <div class="shippingo_dsb-shipping-location"><?php echo esc_html($location_name)?></div>
                            <div class="shippingo_tracking_number_label"><?php echo esc_html(__('Your tracking number:', 'shippingo'))?></div>
                            <div class="shippingo_tracking_number"><?php echo esc_html($tracking_number)?></div>
                            
                        </p>
                        <button class="shippingo_check_status shippingo_open_iframe"  type="button" data-tracking-number="<?php echo esc_html($tracking_number)?>"><?php echo esc_html(__('Check Shipment Status', 'shippingo'));?></button>
                        <button class="shippingo_submit print_label shippingo_open_iframe" data-label="<?php echo esc_html($label_url)?>" type="button" data-tracking-number="<?php echo esc_html($tracking_number)?>"><?php echo esc_html(__('Print Label', 'shippingo'));?></button>
                        <button class="shippingo_submit cancel shippingo_open_iframe"  type="button" data-tracking-number="<?php echo esc_html($tracking_number)?>"><?php echo esc_html(__('Cancel Shipment', 'shippingo'));?></button>
                                                
                        
                    </div>
                <?php
                    
                }
                ?>
                </div>       

               
            </div>
        </div>      

     
        <?php



    }
}


if (!function_exists('shippingo_orders_column'))
{

    function shippingo_orders_column($columns)
    {
       
        $new_columns = array();
        foreach ($columns as $column_name => $column_info)
        {
            
            $new_columns[$column_name] = $column_info;
            if ('order_total' === $column_name)
            {
                $new_columns['shippingo_column'] = '
                            <div>
                            <div><img src="'. plugins_url( '/images/logos/0.png', __FILE__ ) .'"></div>
                            <div>
                            <button type="button" class="shippingo-bulk send shippingo_open_iframe">
                                <span>' .esc_html(__('Bulk Shipments', 'shippingo')). '
                            </button> 
                            '. '<button type="button" class="shippingo-bulk print shippingo_open_iframe">
                                <span>' . esc_html(__('Bulk print Labels', 'shippingo')). '</span>
                            </button>' .'
                            <button type="button" class="shippingo-bulk cancel shippingo_open_iframe">
                                 <span>' . esc_html(__('Bulk cancel Shipments', 'shippingo')). '</span>
                            </button> 
                            </div>
                            <div>
                ';
            }
        }

        return $new_columns;
    }
    add_filter('manage_edit-shop_order_columns', 'shippingo_orders_column', 20);
    add_filter( 'manage_woocommerce_page_wc-orders_columns', 'shippingo_orders_column' );
}

if (!function_exists('shippingo_orders_column_populate')) {

    function shippingo_orders_column_populate_hpos($column,$order) {
        shippingo_orders_column_populate($column,$order);
    }
    function shippingo_orders_column_populate_legacy($column) {
        global $post;
        shippingo_orders_column_populate($column,$post);
    }

    function shippingo_orders_column_populate($column,$post)
    {

         
        

        $shipping_method = shippingo_check_allowed_shipping($post->ID);
        if ($shipping_method == false) {
            return;
        }
        if ($shipping_method==1) {
            $shipping_method = '';
        }

        //$order = wc_get_order($post->ID);

        if ('shippingo_column' === $column) {            

            $shipment = get_post_meta($post->ID, 'shippingo_shipment', true);
            if ($shipment=='') {
                $shipment = [];
            } else {
                $shipment = json_decode($shipment,true);
            }
            ?>
            <div class="shippingo-orders-colum" data-order-id="<?php echo esc_html($post->ID)?>"> 
                
                <div class="shippingo-shipping-orders-container dupe" style="display:none">
                    <button type="button" class="shippingo-dso-row-btn send shippingo_open_iframe">
                        <span><?php echo esc_html(__('Send Shipment', 'shippingo'))?></span>
                    </button>
                    <button type="button" class="shippingo-dso-row-btn edit shippingo_open_iframe">
                        <span><?php echo esc_html(__('Edit Shipment', 'shippingo'))?></span>
                    </button>
                    <span class="shippingo-dso-row-tracking-number"></span>                                      
                    <button type="button" class="shippingo-dso-row-btn print shippingo_open_iframe" data-label="">
                        <span><?php echo esc_html(__('Print Shipment', 'shippingo'))?></span>
                    </button> 
                    <button type="button" class="shippingo-dso-row-btn status shippingo_open_iframe" data-order-id="<?php echo esc_html($post->ID)?>">
                        <span><?php echo esc_html(__('Status Shipment', 'shippingo'))?></span>
                    </button>
                    <button type="button" class="shippingo-dso-row-btn cancel shippingo_open_iframe" data-tracking-number="">
                        <span><?php echo esc_html(__('Cancel', 'shippingo'))?></span>
                    </button>                    
                </div>               
                <?php
                if (!empty($shipment)) {


                    $shipment['label_url'] = shippingo_PRINT_URL.''.$post->ID;
                    
                    ?>
                        <div class="shippingo-shipping-orders-container">
                            <div>
                                <span class="shippingo-dso-row-tracking-number"><?php echo esc_html($shipment['tracking_number'])?></span>                                      
                            </div>
                            <div>
                                <button type="button" class="shippingo-dso-row-btn status shippingo_open_iframe" data-order-id="<?php echo esc_html($post->ID)?>">
                                    <span><?php echo esc_html(__('Status Shipment', 'shippingo'))?></span>
                                </button>  

                                <button type="button" class="shippingo-dso-row-btn print" data-label="<?php echo ($shipment['label_url']!='') ? esc_html($shipment['label_url']) : '';?>">
                                    <span><?php echo esc_html(__('Print Shipment', 'shippingo'))?></span>
                                </button>                                    
                                    
                                <button type="button" class="shippingo-dso-row-btn cancel shippingo_open_iframe" data-tracking-number="<?php echo esc_html($shipment['tracking_number'])?>">
                                    <span><?php echo esc_html(__('Cancel', 'shippingo'))?></span>
                                </button>                                    
                            </div>
                        </div>
                    <?php
                    
                } else {
                    ?>
                    <div class="shippingo-shipping-orders-container">
                        <button type="button" class="shippingo-dso-row-btn send shippingo_open_iframe">
                            <?php echo esc_html(__('Send Shipment', 'shippingo'))?>
                        </button>
                        <span class="shippingo-dso-row-method"><?php echo esc_html($shipping_method)?></span>                   
                    </div>
                    <?php 
                }
                ?>                
            </div>
            <?php
        }
    }
    add_action('manage_shop_order_posts_custom_column', 'shippingo_orders_column_populate_legacy');
    add_action('manage_woocommerce_page_wc-orders_custom_column', 'shippingo_orders_column_populate_hpos', 10, 2);
}





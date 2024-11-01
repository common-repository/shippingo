<?php
/*
    Plugin Name: ShippinGo Ecommerce Delivery - ShippinGo
    Plugin URI: https://www.shippingo.ai
    Description: ShippinGo - ShippinGo Ecommerce Delivery Delivery Plugin
    Author: ShippinGo
    Author URI: 
    Text Domain: shippingo
    Domain Path: /languages
    License: GPLv2 or later
    License URI: https://www.gnu.org/licenses/gpl-2.0.html
    Version: 1.0.16
*/

if ( ! defined( 'WPINC' ) ) {
    die;
}


$shippingo_plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
$token = get_option('shippingo_token', '');
$shippingo_site_url = get_site_url();
define('shippingo_IFRAME',"https://api.shippingo.ai/templates/apps/app/indexStandalone.php?token=$token&site=$shippingo_site_url");
define('shippingo_REST_API',"https://api.shippingo.ai/rest");
define('shippingo_PRINT_URL',shippingo_REST_API."/label?token=$token&order=");
define('shippingo_ID','0');
define('shippingo_version',$shippingo_plugin_data['Version']);


include_once "data.php";
include_once "actions.php";
include_once "orders.php";
include_once "api.php";


function shippingo_activate_plugin() {
    shippingo_register_api_routes();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'shippingo_activate_plugin');


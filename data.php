<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {


    function shippingo_add_plugin_settings_link($actions, $plugin_file)
	{
		// Add settings link only for a specific plugin
        //var_dump($plugin_file);
		if ('shippingo/shippingo.php' === $plugin_file) {
			$settings_link = '<a href="' . esc_url(admin_url('admin.php?page=shippingo-settings')) . '">' . esc_html(__('Settings','shippingo')) . '</a>';
			array_push($actions, $settings_link);
		}
		return $actions;
	}
	add_filter('plugin_action_links', 'shippingo_add_plugin_settings_link', 10, 2);
       
    add_action('admin_menu', 'shippingo_woo_plugin_add_menu_entries');

    function shippingo_woo_plugin_add_menu_entries()
    {
        add_menu_page('Setting', esc_html(__('ShippinGo Ecommerce Delivery','shippingo')), 'edit_posts', 'shippingo-settings', 'shippingo_settings'); 
    }


    function shippingo_settings()
    {
        include 'settings.php';
    }

    function shippingo_action_links($links)
    {

        $links = array_merge(array(
            '<a href="' . esc_url(admin_url('/admin.php?page=shippingo-settings')) . '">' . esc_html(__('Settings','shippingo')) . '</a>'
        ), $links);

        return $links;

    }



    // for plugins<br />
    add_action('init', 'shippingo_load_textdomain_panel');
    function shippingo_load_textdomain_panel()
    {
        load_plugin_textdomain('shippingo', false, basename(dirname(__FILE__)) . '/languages');
    }


 
}





<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
$nonce = wp_create_nonce('shippingo_nonce');

wp_enqueue_style('shippingo-style', plugins_url('/css/style_admin.css', __FILE__) , [],shippingo_version);
wp_enqueue_script('shippingo-settings-script', plugins_url('/js/settings.js', __FILE__) , [],shippingo_version);
wp_localize_script('shippingo-settings-script', 'shippingo_data_settings', 
    array(
        'ajax_url' => admin_url('admin-ajax.php'),
        "settings_error" => esc_html(__('Error: All fields must be filled!', 'shippingo')),
        'nonce'   => $nonce,
    )
);

$shippingo_url_section = (isset($_GET) && isset($_GET['section'])) ? sanitize_text_field(wp_unslash($_GET['section'])) : '';


?>

<div id="shippingo_loader_con" class="hide">
    <div class="shippingo-dsb-spinner"><div></div><div></div><div></div><div></div></div>
    <div id="shippingo_loader_text"><?php echo esc_html(__('Loading settings','shippingo'))?></div>
</div>

<div id="pluginwrap" class="settings-page">
    
    <?php
  
    if (isset($_GET['success'])) {

        $success_msg = esc_html(__('Successfully saved!', 'shippingo'));
        //shippingo_update_locations();

    } else {

        $success_msg = '';


    }



    $token = get_option('shippingo_token', '');
      


    ?>
    <div class="shippingo-dsp-box">
        <div class="shippingo-dsp-box-content">        
            <form id="shippingo_settings_form" 
                    name="shippingo_settings_form" 
                    action="<?php echo  esc_html(admin_url('admin-post.php')) ?>"
                    method="post">

                    

                <div class="shippingo-dsp-license<?php if ($shippingo_url_section!=''){ ?> shippingo-dsp-hide<?php } ?>">

                    <div class="shippingo-dsp-panel">
                        <iframe src="<?php echo esc_html(shippingo_IFRAME)?>" class="shippingo-iframe shippingo-iframe-settings"></iframe>
                    </div>

                </div>

            </form>
        </div>
    </div>
</div>
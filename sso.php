<?php
/*
Plugin Name: SSO
Plugin URI:
Description: Plugin that allows user to connect sing rocketbonds's SSO
Author: Eliot Courtel
Version: 0.1
Author URI: https://sso.rocketbonds.fr
*/

add_action('admin_menu', 'mt_add_pages');


function mt_add_pages() {
    // Add a new submenu under Settings:
    add_options_page(_('SSO', 'rocketbonds-sso'), _('SSO', 'rocketbonds-sso'), 'manage_options', 'rocketbonds_sso', 'sso_settings_page');
    add_action( 'admin_init', 'sso_settings_register' );
}

function sso_settings_register() {
    register_setting( 'rocketbonds_sso_settings', 'sso_front' );
    register_setting( 'rocketbonds_sso_settings', 'sso_back' );
    register_setting( 'rocketbonds_sso_settings', 'sso_register' );
    register_setting( 'rocketbonds_sso_settings', 'sso_key' );
}

function sso_input($option_name){
    $settings = array() ;
    $settings_str = file_get_contents(plugin_dir_path( __FILE__ ) . "/settings.json");
    if ( $settings_str != false ) {
      $settings = json_decode($settings_str, true);
    }
    $key_exist = false;
    if (array_key_exists($option_name, $settings) && strlen($settings[$option_name]) > 0 ) {
        $key = $settings[$option_name];
        $key_exist = true;
    } else {
        $key = get_option($option_name);
    }
    $key = esc_attr($key);
    $ret = '<input type="text" name="' . $option_name . '" value="' . $key . '" ' . ( $key_exist ? 'disabled' : '') .'/>';
    if ( $key_exist ) {
       $ret .= '<input type="hidden" name="' . $option_name . '" value="' . $key . '" />';   
    }
    return $ret;
}

function sso_settings_page() {
    
    echo '<div class="wrap">' .
         '<STYLE> .columns {float: left!important; width: 50%!important;}</STYLE>' .
         '<h1>' . __( 'SSO settings', 'rocketbonds-sso' ) . '</h1>' .
         '<form method="post" action="options.php">';
    settings_fields( 'rocketbonds_sso_settings' );
    do_settings_sections( 'rocketbonds_sso_settings' );
    echo '<table class="form-table">'.
         '<tr class="columns" valign="top">' .
         '<th scope="row">SSO register ID</th>' .
         '<td>' . sso_input('sso_register') . '</td>'.
         '</tr>' .
         '<tr class="columns" valign="top">' .
         '<th scope="row">SSO register KEY</th>' .
         '<td>' . sso_input('sso_key') . '</td>'.
         '</tr>' .
         '<tr class="columns" valign="top">' .
         '<th scope="row">SSO frontend url</th>' .
         '<td>' . sso_input('sso_front') . '</td>'.
         '</tr>' .
         '<tr class="columns" valign="top">' .
         '<th scope="row">SSO backend url</th>' .
         '<td>' . sso_input('sso_back') . '</td>'.
         '</tr>' .
         '</table>' .
         get_submit_button() .
         '</form>' .
         '</div>';
}

register_activation_hook( __FILE__, 'enable_sso' );
register_deactivation_hook( __FILE__, 'disable_sso' );
register_uninstall_hook(__FILE__, 'disable_sso');

function enable_sso() {
       $actual_path = plugin_dir_path( __FILE__ );
       copy($actual_path . '/wp-loginsso.php', ABSPATH . '/wp-loginsso2.php');
       copy($actual_path . '/wp-loginssoadd.php', ABSPATH . '/wp-loginssoadd.php')
}

function disable_sso() {
    $actual_path = plugin_dir_path( __FILE__ );
    unlink(ABSPATH . '/wp-loginsso2.php')
    unlink(ABSPATH . '/wp-loginssoadd.php')
}


?>

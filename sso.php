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

function sso_settings_page() {
    $settings = array() ;
    $settings_str = file_get_contents(plugin_dir_path( __FILE__ ) . "/settings.json");
    if ( $settings_str != false ) {
      $settings = json_decode($settings_str, true);
    }
    echo '<div class="wrap">' .
         '<h1>' . __( 'SSO settings', 'rocketbonds-sso' ) . '</h1>' .
         '<form method="post" action="options.php">' .
         settings_fields( 'rocketbonds_sso_settings' ) .
         do_settings_sections( 'rocketbonds_sso_settings' ) .
         '<table class="form-table">'.
         '<tr valign="top">' .
         '<th scope="row">Registery ID</th>' .
         '<td><input type="text" name="sso_register" value="' . esc_attr( get_option('sso_register') ) . '" /></td>' .
         '</tr>' .
         '<tr valign="top">' .
         '<th scope="row">Registery key</th>' .
         '<td><input type="text" name="sso_key" value="' . esc_attr( get_option('sso_key') ) . '" /></td>' .
         '</tr>' .
         '<tr valign="top">' .
         '<th scope="row">SSO front url</th>' .
         '<td><input type="text" name="sso_bacl" value="' .
         (array_key_exists('sso_back', $settings) ? esc_attr( $settings['sso_back']) : esc_attr( get_option('sso_back') ) )
         . '" ' .
         (array_key_exists('sso_back', $settings) ? 'disabled' : '') .'/></td>' .
         '</tr>' .
         '</table>' .
         submit_button() .
         '</form>' .
         '</div>';
}

?>

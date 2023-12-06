<?php 
/**
 * Plugin Name: Ajax Contact Form + Mailchimp
 * Plugin URI: https://soma-dev.com
 * Version: 1.0
 * Description: A WordPress plugin featuring an AJAX-enabled form for Mailchimp subscription..
 * Author: Soma Moradi
 * Author URI: https://soma-dev.com
 * Text Domain:   cmail
 * Domain Path:   /languages
 * @package MailchimpContactForm
*/

include(plugin_dir_path(__FILE__) . 'admin/settings.php');
include(plugin_dir_path(__FILE__) . 'action.php');
include(plugin_dir_path(__FILE__) . 'functions.php');

load_plugin_textdomain('cmail', false, dirname(plugin_basename(__FILE__)) . '/languages/');

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'mailchimp_ajax_form_settings_link' );
function mailchimp_ajax_form_settings_link( array $links ) {
    $url = get_admin_url() . "admin.php?page=form-settings";
    $settings_link = '<a href="' . $url . '">' . __('Settings', 'cmail') . '</a>';
      $links[] = $settings_link;
    return $links;
}
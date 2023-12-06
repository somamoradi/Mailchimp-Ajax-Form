<?php
/**
 * Plugin Settings page
 *
 * @package MailchimpContactForm
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
// Add the option page
function mailchimp_ajax_form_menu() {
    add_menu_page('mailchimp Ajax Form Settings', 'Mailchimp Ajax Form Settings', 'manage_options', 'form-settings', 'form_settings_page');
}
add_action('admin_menu', 'mailchimp_ajax_form_menu');

// Option page content
function form_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php echo _e('Mailchimp Ajax Form Settings*', 'cmail'); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('form_settings_group'); ?>
            <?php do_settings_sections('form-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings and fields
function mailchimp_ajax_form_settings_init() {
    register_setting('form_settings_group', 'destination_email', 'sanitize_email');
    register_setting('form_settings_group', 'mailchimp_api_key');
    register_setting('form_settings_group', 'mailchimp_list_id');
    register_setting('form_settings_group', 'recaptcha_site_key');
    register_setting('form_settings_group', 'recaptcha_secret_key');

    add_settings_section('form_settings_section', 'Destination Email and Integrations', 'form_settings_section_callback', 'form-settings');

    add_settings_field('destination_email', 'Email Address', 'destination_email_callback', 'form-settings', 'form_settings_section');
    add_settings_field('mailchimp_api_key', 'Mailchimp API Key', 'mailchimp_api_key_callback', 'form-settings', 'form_settings_section');
    add_settings_field('mailchimp_list_id', 'Mailchimp List ID', 'mailchimp_list_id_callback', 'form-settings', 'form_settings_section');
    add_settings_field('recaptcha_site_key', 'reCAPTCHA Site Key', 'recaptcha_site_key_callback', 'form-settings', 'form_settings_section');
    add_settings_field('recaptcha_secret_key', 'reCAPTCHA Secret Key', 'recaptcha_secret_key_callback', 'form-settings', 'form_settings_section');
}
add_action('admin_init', 'mailchimp_ajax_form_settings_init');



// Section callback
function form_settings_section_callback() {
    echo _e('Use <strong>[mailchimp_ajax_form]</strong> to show the contact form in your theme.', 'cmail');
}

// Field callback
function destination_email_callback() {
    $destination_email = get_option('destination_email');
    ?>
    <input type="email" name="destination_email" value="<?php echo esc_attr($destination_email); ?>" />
    <?php
}

// Field callback for Mailchimp API Key
function mailchimp_api_key_callback() {
    $mailchimp_api_key = get_option('mailchimp_api_key');
    ?>
    <input type="text" name="mailchimp_api_key" value="<?php echo esc_attr($mailchimp_api_key); ?>" />
    <?php
}

// Field callback for Mailchimp List ID
function mailchimp_list_id_callback() {
    $mailchimp_list_id = get_option('mailchimp_list_id');
    ?>
    <input type="text" name="mailchimp_list_id" value="<?php echo esc_attr($mailchimp_list_id); ?>" />
    <?php
}

// Field callback for reCAPTCHA Site Key
function recaptcha_site_key_callback() {
    $recaptcha_site_key = get_option('recaptcha_site_key');
    ?>
    <input type="text" name="recaptcha_site_key" value="<?php echo esc_attr($recaptcha_site_key); ?>" />
    <?php
}

// Field callback for reCAPTCHA Secret Key
function recaptcha_secret_key_callback() {
    $recaptcha_secret_key = get_option('recaptcha_secret_key');
    ?>
    <input type="text" name="recaptcha_secret_key" value="<?php echo esc_attr($recaptcha_secret_key); ?>" />
    <?php
}



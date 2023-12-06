<?php 
/**
 * Plugin functions and definitions
 *
 * @package MailchimpContactForm
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$loaded_translations = wp_scripts()->get_data('mailchimp-ajax-form-script', 'data');
// Enqueue scripts
function mailchimp_ajax_form_enqueue_scripts() {
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.4.min.js', array(), null, true);
    wp_enqueue_script('mailchimp-ajax-form-script', plugins_url('assets/js/mailchimp-ajax-form.js', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_style('style', plugins_url('assets/css/style.css', __FILE__), array(), '1.0', 'all');
    wp_enqueue_script('recaptcha','https://www.google.com/recaptcha/api.js', array('jquery'), '1.0', true);

    // Localize script with our options
    $options = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'destination_email' => get_option('destination_email'),
        'mailchimp_api_key' => get_option('mailchimp_api_key'),
        'mailchimp_list_id' => get_option('mailchimp_list_id'),
        'recaptcha_site_key' => get_option('recaptcha_site_key'),
        'recaptcha_secret_key' => get_option('recaptcha_secret_key'),
    );

    wp_localize_script('mailchimp-ajax-form-script', 'ajax_object', $options);

    // Localize the script with translated strings
    wp_localize_script('mailchimp-ajax-form-script', 'cmail_translations', array(
        'messageSent' => __('Your message has been sent!', 'cmail'),
        'emailAdded' => __('Your email has been added to the newsletter.', 'cmail'),
        'emailupdated' => __('You are already in our mailing list. Your details have been updated.', 'cmail'),
        'error' => __('Something wrong happened! Please try again Later.', 'cmail'),
        'notValidEmail' => __('Your e-mail is not valid!', 'cmail'),
        'noCaptcha' => __('Sorry, captcha is not okay!', 'cmail'),
        'captchaFailed' => __('Robot verification failed, please try again.', 'cmail'),
        'recaptchaBox' => __('Please check the reCAPTCHA box.', 'cmail'),
    ));
}
add_action('wp_enqueue_scripts', 'mailchimp_ajax_form_enqueue_scripts');

// Add the form shortcode
function mailchimp_ajax_form_shortcode() {
    ob_start(); 
    include(plugin_dir_path(__FILE__) . 'template/form.php');
    return ob_get_clean();
}
add_shortcode('mailchimp_ajax_form', 'mailchimp_ajax_form_shortcode');


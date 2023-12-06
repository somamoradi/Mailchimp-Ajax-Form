<?php
/**
 * Plugin Form HTML
 *
 * @package MailchimpContactForm
*/
?>
<section id="mailchimp_contact_form_wrap">
    <div class="contact_form_container" id="contact_form_container">
        <form class="mailchimp-ajax-form" id="mailchimp-ajax-form">
            <div class="form_group col_flex">
                <div class="col_6">
                    <label for="fname"><?php echo _e('First Name*', 'cmail'); ?></label>
                    <input class="input_text" type="text" id="fname" name="fname" required>
                </div>
                <div class="col_6">
                    <label for="lname"><?php echo _e('Last Name*', 'cmail'); ?></label>
                    <input class="input_text" type="text" id="lname" name="lname" required>
                </div>
            </div>
            <div class="form_group">
                <label for="email"><?php echo _e('E-mail*', 'cmail'); ?></label>
                <input class="input_text" type="email" id="email" name="email" required>
            </div>

            <div class="form_group">
                <label for="company"><?php echo _e('Company', 'cmail'); ?></label>
                <input class="input_text" type="text" id="company" name="company">
            </div>

            <div class="form_group">
                <label for="message"><?php echo _e('Your message', 'cmail'); ?></label>
                <textarea  class="input_text"id="message" name="message" rows="4"></textarea>
            </div>

            <div class="form_group">
                <div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
            </div>

            <div class="form_group">
                <input class="input_check_box" type="checkbox" id="sub_status" name="sub_status" >
                <label for="sub_status"><?php echo _e('I want to receive news from', 'cmail'); ?> <?php echo get_bloginfo('name'); ?></label>
            </div>

            <div class="form_group">
                <input class="input_submit" type="submit" value="<?php echo _e('Send', 'cmail'); ?>">
            </div>
            
        </form>
    </div>
    <div class="message_wrap">
        <div>
            <span id="back_to_form"></span>
        </div>
        <div class="output_message_wrap">
            <span class="output_message"></span>
        </div>
    </div>
</section>

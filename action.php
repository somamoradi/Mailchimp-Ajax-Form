<?php
/**
 * Plugin Action
 *
 * @package MailchimpContactForm
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
class MailchimpFormHandler {

public function __construct() {
    add_action('wp_ajax_mailchimp_ajax_form_submit', array($this, 'submitForm'));
}

// AJAX handler
    public function submitForm() {
        if (isset($_POST['email'])) {
            // fields value
            $sub_status = sanitize_text_field($_POST['sub_status']);
            $fname = sanitize_text_field($_POST['fname']);
            $lname = sanitize_text_field($_POST['lname']);
            $email = sanitize_email($_POST['email']);
            $company = sanitize_text_field($_POST['company']);
            $message = sanitize_text_field($_POST['message']);
            $rechaptchaResponse = sanitize_text_field($_POST['rechaptcha_response']);
            $lang = get_bloginfo("language");
            // conditions
            $email_valid = filter_var($email, FILTER_VALIDATE_EMAIL);
            // Fields from settings page
            $destination_email = sanitize_email(get_option('destination_email'));
            $apiKey = sanitize_text_field(get_option('mailchimp_api_key'));
            $listID = sanitize_text_field(get_option('mailchimp_list_id'));
            $secretAPIkey = sanitize_text_field(get_option('recaptcha_secret_key'));
            // variables
            $result = [];

            if ($email_valid) {

                // reCAPTCHA validation
                if(isset($rechaptchaResponse) && !empty($rechaptchaResponse)) {
                    // reCAPTCHA response verification
                    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretAPIkey.'&response='.$rechaptchaResponse);
                    // Decode JSON data
                    $response = json_decode($verifyResponse);
                    if($response->success){

                        $this->sendEmail($fname, $lname, $email, $company,$message, $destination_email,$lang,$sub_status, $result);
                        
                        if($sub_status == "true") {
                            $this->sendToMailchimp($fname, $lname, $email, $company, $apiKey, $listID,$lang, $result);
                        }
                    } else {
                        $result['captchaFailed'] = true;
                    }         
                } else{ 
                    $result['recaptchaBox'] = true;
                } 
            }  else{ 
                $result['notvalid'] =true;
            }
            
        }
        echo json_encode($result);
        wp_die();

    }

    private function sendEmail($fname, $lname, $email, $company,$message, $destination_email, $lang,$sub_status, &$result) {
        $protocols = array('http://', 'https://', 'www.');
        $from =  str_replace($protocols, '', get_bloginfo('wpurl'));
        $frommail = 'wordpress@' .$from;
        $isSubscribe = ($sub_status === "true") ? "Yes" : "No";
        $subject = 'New Message From: ' . $fname . " " . $lname . " - " . $from;
        $message_body = "Name: $fname" . " " . "$lname\nEmail: $email\nCompany: $company\nMessage: $message\nFrom Site: $from\nLanguage: $lang\nSubscribe to Mailchimp: $isSubscribe"; 
        $headers = "From: " . $fname . " " . $lname . " <" . $frommail . "> \r\n";
        $sendemail = mail($destination_email, $subject, $message_body, $headers);
        if($sendemail) {
            $result['messageSent'] = true;
        }else {
            $result['error'] = true;
        }
           
    }

    private function sendToMailchimp($fname, $lname, $email, $company, $apiKey, $listID, $lang, &$result) {

        $memberID = md5(strtolower($email));
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;
    
        // Check if the member with the given email already exists
        $existingMember = $this->getMailchimpMember($url, $apiKey);
    
        if ($existingMember) {
            // If the member exists, update the data instead of adding a new member
            $url .= '/';
            $json = json_encode([
                'email_address' => $email,
                'status' => 'subscribed',
                'merge_fields' => [
                    'FNAME' => $fname,
                    'LNAME' => $lname,
                    'COMPANY' => $company,
                    'LANGUAGE' => $lang,
                ],
            ]);
    
            $httpMethod = 'PATCH'; // Use PATCH to update existing data
        } else {
            // If the member doesn't exist, proceed with adding a new member
            $json = json_encode([
                'email_address' => $email,
                'status' => 'subscribed',
                'merge_fields' => [
                    'FNAME' => $fname,
                    'LNAME' => $lname,
                    'COMPANY' => $company,
                    'LANGUAGE' => $lang,
                ],
            ]);
    
            $httpMethod = 'PUT'; // Use PUT for adding a new member
        }
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    
        $resultMailchimp = json_decode(curl_exec($ch), true);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if ($httpCode == 200 || $httpCode == 204 && $existingMember) {
            $result['emailupdated'] = true;
        }else if ($httpCode == 200 || $httpCode == 204){
            $result['emailAdded'] = true;
        }else {
            $result['error'] = true;
        }
    
        curl_close($ch);
    }
    
    // Function to get Mailchimp member details
    private function getMailchimpMember($url, $apiKey) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        $existingMember = json_decode(curl_exec($ch), true);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        curl_close($ch);
    
        return ($httpCode == 200) ? $existingMember : null;
    }
}

$mailHandler = new MailchimpFormHandler();

if (isset($_POST['action']) && $_POST['action'] == 'submitForm') {
    $mailHandler->submitForm(); // Call the submitForm method
}

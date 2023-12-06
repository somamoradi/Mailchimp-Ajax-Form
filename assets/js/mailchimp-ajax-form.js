jQuery(document).ready(function($) {
    var isChecked = false;
    $(".message_wrap").hide();
    $('#sub_status').change(function() {
        isChecked = $(this).prop('checked');
        if (isChecked) {
            $("#sub_status").attr("checked", true);

        } else {
            $("#sub_status").attr("checked", false);
        }
    });

    $('#back_to_form').click(function() {
        $("#contact_form_container").slideDown("slow");
        $(".message_wrap").hide();
    });

    $('#mailchimp-ajax-form').submit(function(event) {
        event.preventDefault();
        var rechaptcha_response = document.getElementById('g-recaptcha-response').value = grecaptcha.getResponse();
        var fname = $('#fname').val();
        var lname = $('#lname').val();
        var email = $('#email').val();
        var company = $('#company').val();
        var message = $('#message').val();
        var sub_status= $('#sub_status').prop('checked');
        let $output_message = $('.output_message');
       
        var form = $(this);
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'submitForm',
                fname: fname,
                lname: lname,
                email: email,
                company: company,
                message: message,
                sub_status: sub_status,
                rechaptcha_response: rechaptcha_response
            },
            success: function(response) {
                let $result = JSON.parse(response);
                $("#contact_form_container").slideUp("slow");
                $(".message_wrap").show();
                
                if ($result.messageSent) {
                    $output_message.text(cmail_translations.messageSent);
                    if ($result.emailAdded) {
                        $output_message.append(cmail_translations.emailAdded);
                    }else if ($result.emailupdated) {
                        $output_message.append(cmail_translations.emailupdated);
                    }
                    form[0].reset();
                } else if ($result.error || $result.else) {
                    $output_message.text(cmail_translations.error);
                } else if ($result.notValidEmail) {
                    $output_message.text(cmail_translations.notValidEmail);
                } else if ($result.noCaptcha) {
                    $output_message.text(cmail_translations.noCaptcha);
                } else if ($result.captchaFailed) {
                    $output_message.text(cmail_translations.captchaFailed);
                } else if ($result.recaptchaBox) {
                    $output_message.text(cmail_translations.recaptchaBox);
                }
            }
        });

        return false;
    });
});
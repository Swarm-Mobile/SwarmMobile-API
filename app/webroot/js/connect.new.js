//connect start
$(function() {
    var ten_off_div = $('.tenOffWrapper');
    var authorized_div = $('.authorized');
    var landing_page_div = $('#landing');
    // alert('You are '+globalMac);
//don't do anything if global mac isn't set
    if (globalMac != '0') {
//get total number of device visits from database
        var visitCount = $.ajax({
            url: "/analytic/device_visits.php",
            dataType: "json",
            data: {
                'token':token,
                'Storeid': memberId,
                'Device': globalMac,
                'parameter': 'Get Device Visits'
            },
            cache: false,
            type: "GET",
            crossDomain: false,
            success: function(response) {
                // console.log(response);
                // alert('Last visits was '+response.LastVisit+', total visits: '+response.Visits);
                // deviceVisits = response.Visits;
                deviceEmail = response.Email;
                last_visit = response.LastVisit;
                landing_page_div.removeClass('loading');
                if (deviceEmail) {
                    //if visits before and email is present, add a new visit, show old landing page
                    authorized_div.show();
                    // ten_off_div.show();
                } else if (connect_screen_variant == "email_gate" || connect_screen_variant == "emailed_deal") {
                    // if visits before but no email, add new visit, but also display ten off if retailer has set it to be like that
                    ten_off_div.show();
                } else {
                    // if no visits, no email, add new user, display ten off if applicable
                    authorized_div.show();
                }
                landing_page_div.removeClass('loading');
                AddVisit();
            },
            error: function(xhr, response, error) {
                //something bad happened, show normal connect screen
                authorized_div.show();
                // alert('Check visits error');
                // console.log(error);
                // console.log(xhr.responseText);
            }
        });
    } else {
        //something weird happened or they are not in a store, so show normal page
        // ten_off_div.show();
        // authorized_div.show();
        if (connect_screen_variant == "email_gate" || connect_screen_variant == "emailed_deal") {
            // if visits before but no email, add new visit, but also display ten off if retailer has set it to be like that
            ten_off_div.show();
        } else {
            // if no visits, no email, add new user, display ten off if applicable
            authorized_div.show();
        }
    }
    ;
});

//on click for $10 off deal, authorize user, log in, send email if applicable
$('#tenOffEmail').live('click', function(e) {
    e.preventDefault();
    var recipient = $('#tenOffRecipient').val();
    // var parentPage = $('#tenoff');
    if (isValidEmail(recipient)) {


        //update email on user profile
        var updateUser = $.ajax({
            url: "/analytic/device_visits.php",
            dataType: "json",
            data: {
                'token':token,
                'Storeid': memberId,
                'Device': globalMac,
                'email': recipient,
                'parameter': 'Update User Profile',
            },
            cache: false,
            type: "GET",
            crossDomain: false,
            success: function(response) {
                // alert('updated user email to '+recipient);
                $('.sendingEmail').append('<br /><p><br/>Success!</p>');
                // console.log(response);
            },
            error: function(xhr, response, error) {
                // $('.sendingEmail').append('<br /><p><br/>Success.</p>');
                // alert('update email error: '+error+' xhr: '+xhr.responseText);
            }
        });
        //send email with ten off coupon and connect them to wifi
        $('.tenOff,.skip').hide();
        $('.sendingEmail').fadeIn(function() {
            // $('.sendingEmail').append('<br /><p><br/>Success!</p>');
            $('.sendingEmail').append('<br><p><br/>Redirecting... </p>');

            if (connect_screen_variant == "emailed_deal") {

                var send_email_request = $.ajax({
                    url: "/app/_ten_off_email/" + memberId + "/" + recipient + "/",
                    dataType: "json",
                    cache: false,
                    type: "GET",
                    crossDomain: false,
                    success: function(response) {
                        // console.log(response);
                        if (response.status == "1") {
                            // $('.sendingEmail').append('<br /><p><br/>Connecting...</p>');
                            LogUserIn();
                        } else {
                            alert('There was an issue sending your email. Please try again.');
                        }
                        ;
                    },
                    error: function(xhr, response, error) {
                        $('#landing').append('<p style="color:#fff">There was an error sending your email. Please try again.</p>');
                        // $('.sendingEmail').append('Error<br />'+error);
                        // $('.sendingEmail').append('Response<br />'+xhr.responseText);
                    }
                });
            } else {
                //dont send email, just login user

                LogUserIn();
            }
        });
    } else {
        alert('Please enter a valid email address');
    }
});
$('.loginLink').live('click', function(e) {
    e.preventDefault();
    var redeem_link = $(this).data('redeem-url');
    LogUserIn(redeem_link);
});
//set opted out users as noise
$('#logOff').live('click', function(e) {
    e.preventDefault();
    if (globalMac) {
        var setNoise = $.ajax({
            url: "/analytic/set_noise.php",
            dataType: "json",
            data: {
                'token':token,
                'mac': globalMac
            },
            cache: false,
            type: "GET",
            crossDomain: false,
            success: function(response) {
                alert('You have successfully opted out. You may now close this window.');
            },
            error: function(xhr, response, error) {
                // alert('user email error: '+error+' xhr: '+xhr.responseText);
            }
        });
    }
});
$('.termsOpenLink').live('click', function(e) {
    e.preventDefault();
    $('#terms').fadeIn();
    window.scrollTo(0, 0);
});
$('#terms header').live('click', function(e) {
    e.preventDefault();
    window.scrollTo(0, 0);
    $('#terms').fadeOut();
});
//end connect
(function() {
    var ga = document.createElement('script');
    ga.type = 'text/javascript';
    ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(ga, s);
})();
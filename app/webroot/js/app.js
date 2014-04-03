var authorized = 'no';
var codesList = $('.codeList');
var codesRedeemed = 0;

$(function() {
// on page load, get total visits && email for the device. If we have visits, open rewards that device is open to. If we have email, open deals. if it's a redemption page that's being loaded (clicked through email link or sent right there from a promo deal), then get the device info first, then, if authorized, redeem the deal
    //show join ML if no content is added/shown
    var dealCount = $('a.product').length;
    if (dealCount < 2) {
        $('#mailingList').show();
    }
    if (globalMac != '0') {
        var visitCount = $.ajax({
            url: "/analytic/device_visits.php",
            dataType: "json",
            data: {
                'token': token,
                'Storeid': memberId,
                'Device': globalMac,
                'Ap_id': ap_id,
                'parameter': 'Get Device Visits',
                'timezone': tz,
                'store_open': store_open,
                'store_close': store_close
            },
            cache: false,
            type: "GET",
            crossDomain: false,
            success: function(response) {
                deviceVisits = response.Sessions;
                deviceEmail = response.Email;
                last_visit = response.LastVisit;
                redemptions = response.Redemptions;
                //if visits, open rewards, display visit count
                AddVisit();
                openRewards();
                //if email, open barcodes
                if (deviceEmail && deviceEmail != 'Guest') {
                    isAuthorized();
                    //loop through device's authorized deals and show them in codes
                    if (redemptions) {
                        for (var i = 0; i < redemptions.length; i++)
                        {
                            appendCodes($('#redeem_' + redemptions[i]));
                        }
                    }
                }
                ;

                is_redeem_page = $.mobile.activePage.hasClass('barcodePage');
                if (is_redeem_page) {
                    redeemPage($.mobile.activePage);
                }
            },
            error: function(xhr, response, error) {
                alert('There was an error');
                // console.log(error);
                // console.log(xhr.responseText);
            }
        });
    } else {
        openRewards();
    }
    ;



});
//display rewards based on device's visit count
function openRewards() {
    if (!deviceVisits) {
        deviceVisits = 1;
    }
    ;
    //loop through rewards to see if device has access
    $('.reward').each(function() {
        var visitNeeded = $(this).data('visits');
        if (deviceVisits >= visitNeeded) {
            //device has access to reward
            $(this).find('.remain').hide();
            $(this).find('.innerCount').addClass('greenBg').text('Available');
        } else {
            //device does not have access
            if ($(this).hasClass('.leadIn')) {
            } else {
                $(this).attr('href', '#');
            }
            $(this).find('.visitsRemaining').text(visitNeeded - deviceVisits);
            $(this).find('.innerCount').text(visitNeeded + ' Visits');
        }
        ;
    });
    //do the same with deals
    $('.deal_redeem').each(function() {
        var visitNeeded = $(this).data('visits');
        if (visitNeeded == 'all') {
            var visit_count = 0;
        } else if (visitNeeded == 'new') {
            var visit_count = 0;
            if (deviceVisits > 1) {
                $(this).hide();
            }
        } else {
            var visit_count = 2;
        }
        if (deviceVisits >= visit_count) {
            //device has access to deal
        } else {
            $(this).hide();
        }
        ;
    });
    // if(deviceVisits){
    if (deviceVisits < 10) {
        $('.counter').html('0' + deviceVisits);
    } else {
        $('.counter').html(deviceVisits);
    }
    ;
    if (deviceVisits > 1) {
        $('.visitCounter').text(deviceVisits + ' Visits');
    } else if (deviceVisits = 1) {
        $('.visitCounter').text(deviceVisits + ' Visit');
    }
    // };
}
//display barcodes if we have an email for the device
function isAuthorized() {
    authorized = 'yes';
    $('.capture').hide();
    $('.barcode').fadeIn();
}
;

// for devices with no saved email, this redeems the deal and update the email for the user profile
$('input.submit_button').live('click', function(e) {
    e.preventDefault();
    // alert('tryings to redeem a deal');
    var thisButton = $(this);
    var parentPage = $(this).parents('.barcodePage');

    // getting dealid from form details to redeem the deal
    var deal_id = thisButton.parents('form').find('.entryCookie').val();
    var member_id = thisButton.parents('form').find('.member_id_Cookie').val();
    var offer_sale_price = thisButton.parents('form').find('.offer_sale_price').val();

    if (offer_sale_price.length == 0) {
        offer_sale_price = 0;
    }

    var emailEntered = thisButton.parents('form').find('.emailInput').val();

    if (isValidEmail(emailEntered)) {

        deviceEmail = emailEntered;
        // ajax call for insert the redeem informtion to the database
        /*
         parameters : email, member_id and entry_id
         */
        $.ajax({
            type: "GET",
            url: "/analytic/redeem.php",
            data: {
                'email': deviceEmail,
                'member_id': member_id,
                'mac': globalMac,
                'entry_id': deal_id,
                'offer_sale_price': offer_sale_price
            },
            success: function(data) {
                // alert(data);
                // alert('rdeemed a deal');
                isAuthorized();
                appendCodes(parentPage);
                // ajax call for updateing user profile information
                /*
                 parameters : email, member_id , entry_id , mac_address and ip_address
                 */
                $.ajax({
                    type: "GET",
                    url: "/analytic/device_visits.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        'token':token,
                        'email': deviceEmail,
                        'Storeid': member_id,
                        'Device': globalMac,
                        'parameter': 'Update User Profile',
                        // 'ip_address': cookie_Ip_Address
                    }
                });
            }
        });

    } else {
        alert('Please enter a valid email address');
    }
    ;
});

$("#landing").live('pageinit', function() {
    $('body').addClass('dark');
});
$("#home").live('pageinit', function() {
    $('body').removeClass('dark');
});
$("#terms").live('pageinit', function() {
    $('body').removeClass('dark');
});

$('.barcodePage').live('pageshow', function(event, obj) {
    redeemPage($(this));
});
function redeemPage(obj) {
    if (deviceEmail && deviceEmail != 'Guest') {
        // alert('appending code');

        appendCodes(obj);
        //page is being shown so try to add a redemption to db, if user is authenticated
        var offer_sale_price = obj.data('offer_sale_price');
        var data_member_id = obj.data('member_id');
        var deal_id = obj.data('deal-id');
        redeem_deal(offer_sale_price, data_member_id, deal_id);
    } else {
        // alert(deviceEmail + ' is null or guest');
    }
    ;
}
//shows the redeemed deal in the codes list
function appendCodes(obj) {
    if (obj.length) {
        var barcodeHtml = obj.find('.barcode').clone().removeClass('hide');
        var checkFor = barcodeHtml.data('id');
        var amount = codesList.find('.barcode[data-id="' + checkFor + '"]').length;
        if (amount) {
            return false;
        }
        ;
        var footerHtml = obj.find('footer').clone().removeClass('hide');
        $('.noCodes').hide();
        codesRedeemed++;
        if (codesRedeemed == 1) {
            $('.notify').fadeIn().html(codesRedeemed);
        } else {
            $('.notify').html(codesRedeemed);
        }
        codesList.append(barcodeHtml).append(footerHtml);
        $('.barcode').css('opacity', '1');
    }
}
;

//add a redemption to the db
function redeem_deal(offer_price, member_id, deal_id) {
    // alert(deviceEmail+' redeeming');
    if (deviceEmail && deviceEmail != 'Guest') {
        $.ajax({
            type: "GET",
            url: "/analytic/redeem.php",
            data: {
                'token':token,
                'email': deviceEmail,
                'member_id': member_id,
                'entry_id': deal_id,
                'mac': globalMac,
                'offer_sale_price': offer_price
            }
        });
    }
    ;
}



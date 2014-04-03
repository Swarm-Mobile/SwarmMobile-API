var loadingDiv = $('#loadingBlocker');
$(function() {
    //responsive menu
    $('#menu-trigger').on('click', function() {
        if ($(window).width() <= 767) {
            if ($('body').hasClass('menu-open')) {
                $('body').removeClass('menu-open');
            } else {
                $('body').addClass('menu-open');
            }
        }
        return false;
    });

    //prevent show/hide clicks from closing dropdown menu
    $('.dropdown-menu input, .dropdown-menu label').click(function(e) {
        e.stopPropagation();
    });

    //dropdown menus
    $('.submenu > a').click(function(e)
    {
        e.preventDefault();
        var submenu = $(this).siblings('ul');
        var li = $(this).parents('li');
        var submenus = $('#sidebar li.submenu ul');
        var submenus_parents = $('#sidebar li.submenu');
        if (li.hasClass('open'))
        {
            if (($(window).width() > 768) || ($(window).width() < 479)) {
                submenu.slideUp();
            } else {
                submenu.fadeOut(250);
            }
            li.removeClass('open');
        } else
        {
            if (($(window).width() > 768) || ($(window).width() < 479)) {
                submenus.slideUp();
                submenu.slideDown();
            } else {
                submenus.fadeOut(250);
                submenu.fadeIn(250);
            }
            submenus_parents.removeClass('open');
            li.addClass('open');
        }
    });

    var ul = $('#sidebar > ul');

    $('#sidebar > a').click(function(e)
    {
        e.preventDefault();
        var sidebar = $('#sidebar');
        if (sidebar.hasClass('open'))
        {
            sidebar.removeClass('open');
            ul.slideUp(250);
        } else
        {
            sidebar.addClass('open');
            ul.slideDown(250);
        }
    });

    $('.preview').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        window.open(url, "DescriptiveWindowName", "resizable=yes,scrollbars=yes,status=yes,width=320,height=540");
    });

    $('.pill_switch button').on('click', function(e) {
        e.preventDefault();
        $(this).parents('.pill_switch').find('button').removeClass('active');
        $(this).addClass('active');
        var valueToSet = $(this).val();
        $(this).parents('.pill_switch').find('input.pillset').val(valueToSet);
    });

    $('.enableButton').click(function(e) {
        e.preventDefault();
        showLoading();
        var currentlyEnabled = $(this).data('status');
        var enableInput = $('input.enableTrigger');
        enableInput.val(currentlyEnabled);

        $('.enableForm').submit();
    });

    var current_member = $('.storesButton .dropdown-menu').data('member');
    var leader_member = $('.storesButton .dropdown-menu').data('leader');
    if (current_member) {
        $('.storesButton .dropdown-menu').find('a[data-member="' + current_member + '"]').append(' <i class="icon icon-eye-open"></i>');
        $('.storesButton .dropdown-menu a,.storeEntry,.logLeadUser').on('click', function(e) {
            e.preventDefault();
            var member_to_lead = $(this).data('member');
            if (member_to_lead) {
                if (leader_member == 333 && member_to_lead != 333) {
                    $('#lead_form select').val(328);
                } else {
                    $('#lead_form select').val(member_to_lead);
                }
                ;
                if (!isAdmin) {
                    mixpanel.track("Viewed Single Store");
                }
                $('#lead_form').submit();
            }
        })
    }

});

function showLoading() {
    loadingDiv.fadeIn();
}
;
function stopLoading() {
    loadingDiv.fadeOut();
}
;
//for time strings, calculate diff in seconds
function splitHMS(hour_string) {
    if (hour_string) {
        var a = hour_string.split(':'); // split it at the colons

        // minutes are worth 60 seconds. Hours are worth 60 minutes.
        var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
        return seconds;
    } else {
        return 0;
    }

}
//after we have diff in seconds, convert the diff to a hms string
function makeHMS(seconds_string) {
    if (seconds_string) {
        d = Number(seconds_string);
        var h = Math.floor(d / 3600);
        var m = Math.floor(d % 3600 / 60);
        var s = Math.floor(d % 3600 % 60);
        return ((h > 0 ? h + ":" : "") + (m > 0 ? (h > 0 && m < 10 ? "0" : "") + m + ":" : "0:") + (s < 10 ? "0" : "") + s);
    }
    ;

}
;
function addCommas(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
function calculatePercentage(oldval, newval, placement, heading, change, progress, currency_new) {
    if (!currency) {
        currency = '$';
    }
    ;
    if (heading == false) {
        placement.empty();
    }
    ;
    percentsavings = ((oldval - newval) / newval) * 100;
    percentchange = Math.round(percentsavings * 100) / 100;

    percentchange = percentchange.toFixed();

    if (isNaN(percentchange)) {
        percentchange = 0;
    }

    actualchange = oldval - newval;
    if (placement.hasClass('percentString')) {
        actualchange = actualchange.toFixed(2);
    } else {
        actualchange = actualchange.toFixed();
    }
    if (placement.hasClass('secondString')) {
        // deal with seconds difference
        if (actualchange < 0) {
            var makeNeg = true;
        }
        actualchange = Math.abs(actualchange);
        actualchange = makeHMS(actualchange);
        if (typeof actualchange != 'undefined') {
            if (makeNeg) {
                actualchange = '-' + ((currency_new) ? currency : '') + actualchange;
            } else {
                actualchange = '+' + ((currency_new) ? currency : '') + actualchange;
            }
        } else {
            actualchange = ((currency_new) ? currency : '') + '0';
        }        
    } else {
        if (actualchange > 0) {
            actualchange = '+' + ((currency_new) ? currency : '') + addCommas(actualchange);
        } else if (actualchange < 0) {
            actualchange = Math.abs(actualchange);            
            actualchange = '-' + ((currency_new) ? currency : '') + addCommas(actualchange);
        } else {
            actualchange = ((currency_new) ? currency : '') + '0';
        }
        ;

    }


    if (percentchange > 0) {
        percentarrow = '+'
        percentstatus = 'text-success';
    } else if (percentchange < 0) {
        percentarrow = ''
        percentstatus = 'text-danger';
    } else {
        percentarrow = '&nbsp;';
        percentstatus = 'text';
    }
    if (percentchange == '-Infinity') {
        percentchange = '100';
    }
    if (!$.isNumeric(percentchange)) {
        percentchange = '100';
    }

    if (change) {
        change_str = '<p class="changeOverview ' + percentstatus + '">' + actualchange + ' (' + percentarrow + ' ' + percentchange + '%)</p>';
    } else {
        change_str = '<p class="changeOverview ' + percentstatus + '"><span>' + percentarrow + '</span>' + percentchange + '%</p>';
    }
    if (placement.hasClass('dollarString')) {
        placement.find('h2').html(currency + addCommas(oldval));
    } else {
        placement.find('h2').html(addCommas(oldval));
    }
    placement.append(change_str);

    if (progress) {
        // console.log(placement);
        var progress_bar = placement.parents('.dataRow').find('.progress');
        // if(percentchange>=0){
        //   progress_bar.removeClass('progress-danger').addClass('progress-success');
        // } else {
        //   progress_bar.removeClass('progress-success').addClass('progress-danger');
        // }
        percentchange = Math.abs(percentchange);
        progress_bar.find('.progress-bar').css('width', percentchange + '%');
    }
}
function prettyDate(date_string) {
    var make_pretty = new Date(date_string);
    make_pretty = make_pretty.format("mmm d, yyyy");
    return make_pretty;

}
;
function isInt(n) {
    return n % 1 === 0;
}
function days_between(date1, date2) {
    first_date = new Date(date1);
    second_date = new Date(date2);
    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24;

    // Convert both dates to milliseconds
    var date1_ms = first_date.getTime();
    var date2_ms = second_date.getTime();

    // Calculate the difference in milliseconds
    var difference_ms = Math.abs(date1_ms - date2_ms);

    // Convert back to days and return
    return Math.round(difference_ms / ONE_DAY);

}
;
//validate store hours
function validateTimes(submitOnComplete) {
    $('.time').each(function() {
        var thetime = $(this).val();
        var action = $(this).data('action');
        if (action == 'close') {
            var open_time = $(this).prevAll('.time').val();
            if (open_time >= thetime && open_time != 0 && thetime != 0) {
                $(this).parents('.form-group').addClass('error');
            } else {
                $(this).parents('.form-group').removeClass('error');
            }
        }
    });
    var error_count = $('.form-group.error').length;
    if (error_count) {
        //do nothing
        return false;

    } else if (submitOnComplete) {
        showLoading();
        $('#hoursForm').submit();
    } else if (!submitOnComplete) {
        return true;

    }
}


/*
 Function and click handler to set user preference array as cookies to hide/display graphs/metrics/etc... 
 
 @cookieObject => json array of preferences that need to be set. Adding to this array will set 
 @classToToggle => the class to toggle if the checkbox is checked/unchecked, defaults to bootstrap hidden on mobile/shown on mobile (visible-xs visible-sm)
 @theCookie => The name of the cookie to set, defaults to dashboard_filters
 @theFieldset => the fieldset to check for preferences, defaults to .userPreferencesFieldset by default
 */

//register clicks on the checkboxes, to prevent hide/show you can toggle disabled class in the html for the filters
$('.userPreferencesFieldset input[type="checkbox"]').on('click', function() {
    var theCookie = $('.userPreferencesFieldset').data('cookie-name');
    var classToToggle = $('.userPreferencesFieldset').data('cookie-class');
    var inputs = $('.userPreferencesFieldset :input[type="checkbox"]:not(:disabled)');
    var serializedCheckboxData = $.map(inputs, function(n, i)
    {
        var o = {};
        o[n.name] = ($(n).is(':checked') ? 1 : 0);
        return o;
    });
    setCookieFilters(serializedCheckboxData, classToToggle, theCookie);
    if (!isAdmin) {
        mixpanel.track("Metric Viewing Preferences Changed");
    }

});

function setCookieFilters(cookieObject, classToToggle, theCookie, theFieldset) {
    //if we have an object being passed, use it, if not then set to defaults
    if (!cookieObject) {
        cookieObject = [
            {WindowConversion: 1},
            {ReturningShoppers: 1},
            {AvgDwell: 0},
            {ConversionRate: 1},
            {AvgTicket: 1},
            {ItemsPerTransaction: 0}
        ];
    }
    //set default class for preferences form
    if (!classToToggle) {
        classToToggle = 'visible-xs visible-sm';
    }
    if (!theFieldset) {
        theFieldset = $('.userPreferencesFieldset');
    }
    if (!theCookie) {
        theCookie = 'dashboard_filters';
    }
    // loop through each property, find the form input, add hide class based on value. We're not actually hiding we're just hiding on desktop for visual happiness
    for (var theFilterCount in cookieObject) {


        var theMetric = cookieObject[theFilterCount];
        for (var theProperty in theMetric) {
            var theValue = cookieObject[theFilterCount][theProperty];

            var theCheckbox = theFieldset.find('input[name="' + theProperty + '"]');
            //hack: fixes graph width if the drop down menu overlays on the graph div
            $(document).resize();
            //find boolean for the metric, hide if needed, show if needed
            if (theValue) {
                theCheckbox.prop('checked', true);
                $('.' + theProperty).removeClass(classToToggle);
            } else {
                theCheckbox.prop('checked', false);
                $('.' + theProperty).addClass(classToToggle);

            }
        }

    }
    //set or reset the cookie
    $.cookie(theCookie, JSON.stringify(cookieObject), {path: '/'});
}

/**
 * Submits content area of website via AJAX to pdf export script
 */
$(function() {


    // Get parameters for pdf export
    function get_pdf_params() {
        // Clone the HTML of the page
        var html = $('html').clone();

        // hide mouse tracking paths
        html.find('path.highcharts-tracker').hide();

        var host = window.location.protocol + '//' + window.location.host;

        // Change all relative urls to absolute
        html.find('a, link').not('[href^="http"],[href^="https"],[href^="mailto:"],[href^="#"]').each(function() {
            $(this).attr('href', function(index, value) {
                if (value.substr(0, 1) !== "/") {
                    value = window.location.pathname + value;
                }

                return host + value;
            })
        })

        // Remove scripts
        html.find('script').remove();

        var result = {
            html: encodeURIComponent(html.prop('outerHTML')),
            date_start: $('input[name="daterangepicker_start"]').val(),
            date_end: $('input[name="daterangepicker_end"]').val(),
            page_name: $('#page-name').text(),
            member_id: member_id,
            obfuscate: 'dfkajhfj3435kJ'
        }

        return result;
    }

    // when user presses export to pdf button, issue a download JavaScript call with the pdf parameters
    $('#pdfButton').on('click', function(e) {
        var pdf_params = get_pdf_params();
        $.download(
                '/pdf_export/download',
                'html=' + pdf_params.html
                + '&member_id=' + pdf_params.member_id
                + '&obfuscate=' + pdf_params.obfuscate
                + '&date_start=' + pdf_params.date_start
                + '&date_end=' + pdf_params.date_end
                + '&page_name=' + pdf_params.page_name,
                'post'
                );
        if (!isAdmin) {
            mixpanel.track(pdf_params.page_name + " PDF Exported");
        }
        e.preventDefault();
    });

    $('.emailPdfButton').on('click', function(e) {
        $('#emailModal').modal('show');
        e.preventDefault();
    });

    $('#exportEmailForm').on('submit', function(e) {
        $('#emailModal .alert-danger').remove();
        $('#exportEmailForm #toEmail').attr('disabled', 'disabled');
        $('#exportEmailForm #emailMessage').attr('disabled', 'disabled');
        $('#emailModal button').attr('disabled', 'disabled');
        $('#exportEmailForm #sendEmailPdf').html('<img src="/b2b/images/ajax-loader2.gif"/>');
        $('#emailModal').data('bs.modal').options.keyboard = false;
        $('#emailModal').data('bs.modal').options.backdrop = 'static';

        var pdf_params = get_pdf_params();
        pdf_params.email_to = $('#exportEmailForm #toEmail').val();
        pdf_params.email_message = $('#exportEmailForm #emailMessage').val();
        e.preventDefault();

        $.post(
                '/pdf_export/email',
                'html=' + pdf_params.html
                + '&member_id=' + pdf_params.member_id
                + '&obfuscate=' + pdf_params.obfuscate
                + '&date_start=' + pdf_params.date_start
                + '&date_end=' + pdf_params.date_end
                + '&page_name=' + pdf_params.page_name
                + '&email_to=' + pdf_params.email_to
                + '&email_message=' + pdf_params.email_message
                ,
                'json'
                ).always(function() {
            $('#exportEmailForm #toEmail').removeAttr('disabled');
            $('#exportEmailForm #emailMessage').removeAttr('disabled');
            $('#emailModal button').removeAttr('disabled');
            $('#exportEmailForm #sendEmailPdf').html('Send');
            $('#emailModal').data('bs.modal').options.keyboard = true;
            $('#emailModal').data('bs.modal').options.backdrop = true;
        })
                .fail(function(xhr, textStatus, errorThrown) {
                    $('#exportEmailForm').prepend('<div class="alert alert-danger">Error: ' + xhr.statusText + '</div>');
                })
                .done(function(data) {
                    if (typeof data !== 'object') {
                        data = $.parseJSON(data);
                    }

                    if (!data.success) {
                        $('#exportEmailForm').prepend('<div class="alert alert-danger">' + data.message + '</div>');
                    } else {
                        $('#emailModal').modal('hide');
                        $('#exportEmailForm #toEmail').val('');
                        $('#content').prepend('<div class="container-fluid hidden-print"><div class="alert alert-success alert-dismissable alert-top"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.message + '</div></div>')
                        if (!isAdmin) {
                            mixpanel.track(pdf_params.page_name + " PDF Emailed");
                        }

                    }
                });

    });


})
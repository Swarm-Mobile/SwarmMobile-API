var sloadLoadingTime;
var showSlowLoadingDuration = 5000;

/* Datatables plugin for bootstrap pagination */
$.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings)
{
    return {
        "iStart": oSettings._iDisplayStart,
        "iEnd": oSettings.fnDisplayEnd(),
        "iLength": oSettings._iDisplayLength,
        "iTotal": oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage": oSettings._iDisplayLength === -1 ?
                0 : Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
        "iTotalPages": oSettings._iDisplayLength === -1 ?
                0 : Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
    };
}

/* Bootstrap style pagination control */
$.extend($.fn.dataTableExt.oPagination, {
    "bootstrap": {
        "fnInit": function(oSettings, nPaging, fnDraw) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function(e) {
                e.preventDefault();
                if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
                    fnDraw(oSettings);
                }
            };

            $(nPaging).addClass('pagination').append(
                    '<ul class="pagination">' +
                    '<li class="prev disabled"><a href="#">&laquo;</a></li>' +
                    '<li class="next disabled"><a href="#">&raquo; </a></li>' +
                    '</ul>'
                    );
            var els = $('a', nPaging);
            $(els[0]).bind('click.DT', {action: "previous"}, fnClickHandler);
            $(els[1]).bind('click.DT', {action: "next"}, fnClickHandler);
        },
        "fnUpdate": function(oSettings, fnDraw) {
            var iListLength = 5;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);

            if (oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            }
            else if (oPaging.iPage <= iHalf) {
                iStart = 1;
                iEnd = iListLength;
            } else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }

            for (i = 0, iLen = an.length; i < iLen; i++) {
                // Remove the middle elements
                $('li:gt(0)', an[i]).filter(':not(:last)').remove();

                // Add the new list items and their event handlers
                for (j = iStart; j <= iEnd; j++) {
                    sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
                    $('<li ' + sClass + '><a href="#">' + j + '</a></li>')
                            .insertBefore($('li:last', an[i])[0])
                            .bind('click', function(e) {
                                e.preventDefault();
                                oSettings._iDisplayStart = (parseInt($('a', this).text(), 10) - 1) * oPaging.iLength;
                                fnDraw(oSettings);
                            });
                }

                // Add / remove disabled classes from the static elements
                if (oPaging.iPage === 0) {
                    $('li:first', an[i]).addClass('disabled');
                } else {
                    $('li:first', an[i]).removeClass('disabled');
                }

                if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
                    $('li:last', an[i]).addClass('disabled');
                } else {
                    $('li:last', an[i]).removeClass('disabled');
                }
            }
        }
    }
});

$(function() {

    /* check for encoded hash (if back buton clicked etc... ) */
    encodedHash = getParameterByName("q");
    startCookieVal = $.cookie('start_range_1');
    endCookieVal = $.cookie('end_range_1');

    /* if theres a hash and it's not a demo */
    if (encodedHash && demo == "no") {

        postData = $.parseJSON(base64_decode(encodedHash));

        if (typeof (startCookieVal) != 'undefined' && typeof (endCookieVal) != 'undefined' && startCookieVal != null && endCookieVal != null) {

            startCookieValFormatted = Date.parse(startCookieVal).toString('MM/dd/yyyy');
            endCookieValFormatted = Date.parse(endCookieVal).toString('MM/dd/yyyy');

            if (startCookieValFormatted != postData.startdate) {
                postData.startdate = startCookieValFormatted;
            }
            if (endCookieVal != postData.enddate) {
                postData.enddate = endCookieValFormatted;
            }

            postData.startdate;
            postData.enddate;

        }

        arrStart = postData.startdate.split("/");
        arrEnd = postData.enddate.split("/");
        startDate = new Date(arrStart[2], arrStart[0] - 1, arrStart[1]);
        endDate = new Date(arrEnd[2], arrEnd[0] - 1, arrEnd[1]);

        setReportTitle(startDate, endDate);
        start_range_1 = postData.startdate;
        end_range_1 = postData.enddate

        $.cookie('start_range_1', startDate.toString('yyyy-MM-dd'), {path: '/'});
        $.cookie('end_range_1', endDate.toString('yyyy-MM-dd'), {path: '/'});

        /* else default date range and no demo still */
    } else if (typeof (startCookieVal) != 'undefined' && typeof (endCookieVal) != 'undefined' && startCookieVal != null && endCookieVal != null && demo == "no") {




        setReportTitle(Date.parse(startCookieVal), Date.parse(endCookieVal));
        start_range_1 = Date.parse(startCookieVal).toString('MM/dd/yyyy');
        end_range_1 = Date.parse(endCookieVal).toString('MM/dd/yyyy');



        /* cookies didn't work or it's a demo */
    } else {


        if (demo == "no") {
            start_range_1 = Date.today().add({days: -7});
            end_range_1 = Date.today().add({days: -1});
        } else {
            end_range_1 = new Date(2013, 05, 26);
            start_range_1 = new Date(2013, 05, 26).add({days: -6});
        }

        setReportTitle(start_range_1, end_range_1);
        $.cookie('start_range_1', start_range_1.toString('yyyy-MM-dd'), {path: '/'});
        $.cookie('end_range_1', end_range_1.toString('yyyy-MM-dd'), {path: '/'});

        start_range_1 = start_range_1.toString('MM/dd/yyyy');
        end_range_1 = end_range_1.toString('MM/dd/yyyy');
    }
    ;


    showTableLoading();


    //define range picker with normal options
    $('#range_a').daterangepicker(rangepicker_options,
            function(start, end) {
                start = start.toDate();
                end = end.toDate();

                $.cookie('start_range_1', start.toString('yyyy-MM-dd'));
                $.cookie('end_range_1', end.toString('yyyy-MM-dd'));
                start_range_1 = start.toString('MM/dd/yyyy');
                end_range_1 = end.toString('MM/dd/yyyy');
                setReportTitle(start, end);
                showTableLoading();
                set_default_profile_data(false);

                if (!isAdmin) {
                    mixpanel.track("Consumer Profiles Dates Changed");
                }

            }
    );

    if (!encodedHash || demo == "yes") {
        set_default_profile_data(true);
    } else {
        initFilters();
        var filterTypes = {"pos": ["minPurchase", "class", "minPurchaseCount", "sku", "family"], "global": ['hasemail'], "network": ['dwell', 'visits', 'connected', 'redeemed']};

        hasFilters = false;
        for (var filterType in filterTypes) {
            for (var i = 0; i < filterTypes[filterType].length; i++) {
                if (typeof (postData["filters[" + filterType + "][" + filterTypes[filterType][i] + "]"]) != "undefined" && postData["filters[" + filterType + "][" + filterTypes[filterType][i] + "]"] != "") {
                    var $checkbox = $('#' + filterTypes[filterType][i] + '-checkbox');
                    var $field = $('#' + filterTypes[filterType][i] + '-filter');
                    $field.val(postData["filters[" + filterTypes[filterType][i] + "]"]);
                    $checkbox.attr('checked', 'checked');
                    var $filterContainer = $('div.filter_switch_new, div.filter_switch');
                    toggleFilters($filterContainer);
                }
            }
        }
        loadShopperProfiles(postData);
    }

    var $filterContainer = $('div.filter_switch_new, div.filter_switch');
    $('#showFiltersButton').bind('click', function() {
        toggleFilters($filterContainer);
    });


    $('.drawGraphs').on('click', function() {
        if ($('#date_alert_1').is(':visible')) {
            //do nothing
        } else {
            showTableLoading();
            set_default_profile_data(true);
        }
        ;
    });

    $("#tableLoad").on("change", "#export_all_toggle", function(e) {
        var cbState = $(this).is(":checked");
        $(".export_toggle").prop("checked", cbState);
    });

    $('#tableLoad').on('click', '#report tr td.linked', function(e) {
        e.preventDefault();
        var user_selected = $(this).parents('tr').data('mac');
        var count = $(this).parents('tr').prevAll('tr').length;
        if (count == 1 && demo == "yes") {
            window.location = '/profiles/detail/';
        } else if (typeof (user_selected) != 'undefined') {
            window.location = '/shoppers/profile/' + user_selected;
        }
    });

});



set_default_profile_data = function(checkFilters) {




    initFilters();


    var postData = {
        'Storeid': member_id,
        'format': 'json',
        'parameter': 'Get Shopper Profiles',
        'startdate': start_range_1,
        'enddate': end_range_1,
        'filter': filter
    };
    var filterTypes = {"pos": ["minPurchase", "class", "minPurchaseCount", "sku", "family"], "global": ['hasemail'], "network": ['dwell', 'visits', 'connected', 'redeemed']};


    hasFilters = false;
    for (var filterType in filterTypes) {
        for (var i = 0; i < filterTypes[filterType].length; i++) {
            var $checkbox = $('#' + filterTypes[filterType][i] + '-checkbox');
            var $field = $('#' + filterTypes[filterType][i] + '-filter');
            if ($checkbox.is(':checked') && (!$field.length || ($field.val() != "" && $field.val() != null))) {
                fieldVal = $field.length ? $field.val() : '1';
                postData["filters[" + filterType + "][" + filterTypes[filterType][i] + "]"] = fieldVal;
                hasFilters = true;
            }
        }
    }

    if (hasFilters && checkFilters) {
        toggleFilters($('div.filter_switch_new, div.filter_switch'));
    }
    loadShopperProfiles(postData);
}
loadShopperProfiles = function(postData) {
    // $('#report').empty();
    postData.token = token;
    if ($('div.filter_switch_new').length) {
        var shopperProfiles = $.ajax({
            url: "/analytic/get_shopper_profiles.php",
            data: postData,
            type: "GET",
            cache: false,
            success: function(data) {
                clearTimeout(slowLoadingTimeout);
                $('#tableLoad').html(data);
                //sort table based on what filter is selected

                //$("#AllVisits").tablesorter({sortList: [[(filter=='purchased') ? 3 : 2,1]]});
                $('#AllVisits').dataTable({
                    "aaSorting": [[3, "desc"]],
                    "bFilter": false,
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    "sPaginationType": "bootstrap",
                    "sDom": '<"top">rt<"bottom"filp><"clear">'
                });

                if (filter == 'redeemed') {
                    $('#report tr').each(function() {
                        var redeem_count = $(this).data('redemptions');
                        if (!redeem_count || redeem_count == 0) {
                            $(this).hide();
                        }
                    });
                }
                var resort = true;
                var sorting = [0, 0];
                $.cookie('start_range_1', postData.startdate.toString('yyyy-MM-dd'), {path: '/'});
                $.cookie('end_range_1', postData.enddate.toString('yyyy-MM-dd'), {path: '/'});
                History.replaceState(postData, document.title, '?q=' + base64_encode($.toJSON(postData)));
                $("#AllVisits").trigger("update", [resort]);
                // $("#AllVisits").trigger("sorton", [sorting]);
                stopLoading();

            }
        });
    }

    if (demo == "yes") {
        return true;
    }
    var performaceIndicators = $.ajax({
        url: "/analytic/get_shopper_profiles.php",
        data: {
            'token':token,
            'Storeid': member_id,
            'format': 'json',
            'parameter': 'Get Performance Indicators',
            'startdate': start_range_1,
            'enddate': end_range_1

        },
        type: "GET",
        dataType: "json",
        cache: false,
        success: function(data) {
            var Transactions = data.Transactions;
            $('.Transactions').html(Transactions);
            var Revenue = data.Revenue;
            $('.Revenue').html(currency + Revenue);
            var ItemsPerTransaction = data.ItemsPerTransaction;
            $('.ItemsPerTransaction').html(ItemsPerTransaction);
            var AvgTicket = data.AvgTicket;
            $('.AvgTicket').html(currency + AvgTicket);


            /* var TopSites = data.TopSites;
             var NumSites = TopSites.length;
             $('.site_list tbody').html('');
             if(NumSites>1){
             for (var i = 0; i < NumSites; i++) {
             $('.site_list table tbody').append('<tr><td>'+TopSites[i]['Site']+'</td><td>'+TopSites[i]['Visits']+'</td></tr>');
             };
             } else {
             $('.site_list table tbody').html('<tr><td colspan="2" class="text-center">There is no browsing data from these dates.</td></tr>');
             } */

            $('span.show_menu_option').bind('click', function() {

            });
        }
    });

};
var initFilters = function() {

    $('.filterContainer input[type="text"], .filterContainer select').bind('change', function(event) {
        $this = $(this);
        $thisId = $this.attr('id').replace('-filter', '');
        if ($this.val() != "") {
            $('#' + $thisId + '-checkbox').attr('checked', 'checked');
        } else {
            $('#' + $thisId + '-checkbox').attr('checked', false);
        }
    });

    $.ajax({
        url: "/analytic/get_shopper_profiles.php",
        data: {
            'token':token,
            'Storeid': member_id,
            'format': 'json',
            'parameter': 'Get Customer Filters',
            'startdate': start_range_1,
            'enddate': end_range_1,
            'filter': filter
        },
        type: "GET",
        cache: false,
        success: function(data) {
            populateFilterSelects("class", data.classes);
            populateFilterSelects("family", data.families);
            $('.filterContainer input[type="text"]').each(function() {
                $this = $(this);
                $thisId = $this.attr('id').replace('-filter', '');
                if (!$('#' + $thisId + '-checkbox').is(':checked')) {
                    $this.val('');
                }
            });
        }
    });
}

var populateFilterSelects = function(fieldName, data) {
    $select = $('#' + fieldName + '-filter');
    oldVal = $select.val();
    $select.html('');
    if (data.length) {
        $select.append($('<option value=""></option>'));
        for (var index = 0; index < data.length; index++) {
            $option = $('<option value="' + data[index] + '">' + data[index] + '</option>');
            if ($('#' + fieldName + '-checkbox').is(':checked') && oldVal == data[index]) {
                $option.attr('selected', 'true');
            }
            $select.append($option);
        }
        $('div#' + fieldName + 'FilterContainer').css('display', 'block');
    } else {
        $('div#' + fieldName + 'FilterContainer').hide();
    }
}



function showTableLoading() {
    if (demo == "no") {
        $('.focusNumber').html('<img src="/b2b/images/ajax-loader.gif"/>');
    }
    $('#tableLoad').html('<div class="loadingTable"></div>');
    slowLoadingTimeout = setTimeout(showSlowLoadingMessage, showSlowLoadingDuration);
}

function showSlowLoadingMessage() {
    $('#tableLoad .loadingTable').append('<div class="slowLoadingMessage">This is taking longer than usual.  Change your filter criteria or date window to speed this up.</div>');
}

var toggleFilters = function($filterContainer) {
    if ($filterContainer.is(':visible') && ($filterContainer.hasClass('filter_switch') || !$('div.filter_switch_new input:checked').length)) {
        $filterContainer.slideUp();
    } else {
        $filterContainer.slideDown();
    }
}

base64_encode = function(data) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
            ac = 0,
            enc = "",
            tmp_arr = [];

    if (!data) {
        return data;
    }

    do { // pack three octets into four hexets
        o1 = data.charCodeAt(i++);
        o2 = data.charCodeAt(i++);
        o3 = data.charCodeAt(i++);

        bits = o1 << 16 | o2 << 8 | o3;

        h1 = bits >> 18 & 0x3f;
        h2 = bits >> 12 & 0x3f;
        h3 = bits >> 6 & 0x3f;
        h4 = bits & 0x3f;

        // use hexets to index into b64, and append result to encoded string
        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
    } while (i < data.length);

    enc = tmp_arr.join('');

    var r = data.length % 3;

    return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);

}

function base64_decode(data) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
            ac = 0,
            dec = "",
            tmp_arr = [];

    if (!data) {
        return data;
    }

    data += '';

    do { // unpack four hexets into three octets using index points in b64
        h1 = b64.indexOf(data.charAt(i++));
        h2 = b64.indexOf(data.charAt(i++));
        h3 = b64.indexOf(data.charAt(i++));
        h4 = b64.indexOf(data.charAt(i++));

        bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

        o1 = bits >> 16 & 0xff;
        o2 = bits >> 8 & 0xff;
        o3 = bits & 0xff;

        if (h3 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1);
        } else if (h4 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1, o2);
        } else {
            tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
        }
    } while (i < data.length);

    dec = tmp_arr.join('');

    return dec;
}


getParameterByName = function(name) {

    var search = location.search;
    if (!search) {
        var urlParts = document.location.href.split('?');
        if (typeof (urlParts[1]) != 'undefined') {
            search = '?' + urlParts[1];
        }
    }
    var name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
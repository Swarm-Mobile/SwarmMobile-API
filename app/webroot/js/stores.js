(function() {
    var originalAddClassMethod = jQuery.fn.addClass;
    jQuery.fn.addClass = function() {
        var result = originalAddClassMethod.apply(this, arguments);
        jQuery(this).trigger('cssClassChanged');
        return result;
    };
})();

$(function() {
    checkboxes = JSON.parse($.cookie('stores_filters'));
    if (checkboxes != null) {
        $.each(checkboxes, function(i, checkbox) {
            $.each(checkbox, function(k, v) {
                if (v) {
                    $('input[name="' + k + '"]').prop('checked', true);
                }
            });
        });
    }
    setStoreDisplay();
    $("#AllStores").tablesorter({
        textExtraction: function(node) {
            return node.getAttribute('data-sorter');
        }
    });
    $('#AllStores thead tr td').on('cssClassChanged', function() {
        var index = $(this).parent().find('td').index(this);
        var add = ($(this).hasClass('tablesorter-headerDesc')) ? 'tablesorter-headerDesc' : 'tablesorter-headerAsc';
        var remove = ($(this).hasClass('tablesorter-headerDesc')) ? 'tablesorter-headerAsc' : 'tablesorter-headerDesc';        
        $(this).parents('thead').find('tr').each(function() {            
            $(this).find('th').eq(index).addClass(add);
            $(this).find('th').eq(index).removeClass(remove);
        });
    });

    var sorting = [[4, 1]];
    $("#AllStores").trigger("sorton", [sorting]);

    getStoreData(start_range_1, end_range_1, start_range_2, end_range_2);

    $('#range_a').daterangepicker(rangepicker_options,
            function(start, end) {
                start = start.toDate();
                end = end.toDate();
                //pretty dates
                var start_date = start.toString('MMMM dS, yyyy');
                var end_date = end.toString('MMMM dS, yyyy');

                //ugly dates
                start_range_1 = start.toString('yyyy-MM-dd');
                end_range_1 = end.toString('yyyy-MM-dd');

                var range_of_days = days_between(start, end) + 1;
                if (range_of_days == 1) {
                    start_range_2 = Date.parse(start_date).add({days: -7}).toString('yyyy-MM-dd');
                    end_range_2 = Date.parse(start_date).add({days: -7}).toString('yyyy-MM-dd');
                } else {
                    start_range_2 = Date.parse(start_date).add({days: -range_of_days}).toString('yyyy-MM-dd');
                    end_range_2 = Date.parse(start_date).add({days: -1}).toString('yyyy-MM-dd');
                }
                ;
                var db_start = start.toString('yyyy-MM-dd');
                var db_end = end.toString('yyyy-MM-dd');


                getStoreData(start_range_1, end_range_1, start_range_2, end_range_2);
                start_range_2 = Date.parse(start_range_2);
                end_range_2 = Date.parse(end_range_2);
                setReportTitle(start, end, start_range_2, end_range_2);
                $.cookie('start_range_1', db_start, {path: '/'});
                $.cookie('end_range_1', db_end, {path: '/'});
                if (!isAdmin) {
                    mixpanel.track("Global Dashboard Dates Changed");
                }
            }
    );

    $('.storesPreferencesFieldset input[type="checkbox"]').on('click', function() {
        var theCookie = $('.storesPreferencesFieldset').data('cookie-name');
        var classToToggle = $('.storesPreferencesFieldset').data('cookie-class');
        var inputs = $('.storesPreferencesFieldset :input[type="checkbox"]:not(:disabled)');
        var serializedCheckboxData = $.map(inputs, function(n, i)
        {
            var o = {};
            o[n.name] = ($(n).is(':checked') ? 1 : 0);
            return o;
        });
        setCookieFilters(serializedCheckboxData, classToToggle, theCookie);
        setStoreDisplay();
    });

});

function setStoreDisplay() {
    $('.storesPreferencesFieldset input[type="checkbox"]').each(function() {
        $('#AllStores .' + $(this).attr('name')).css('display', $(this).is(':checked') ? 'table-cell' : 'none');
    });
}

function getStoreData(start_date, end_date, start_date_2, end_date_2) {
    var totals = {cI: {}, lI: {}};
    var store_num = 0;
    $('tr.storeEntryTotal td.dataCell').empty();
    $('tr.storeEntryTotal').addClass('loading');
    $('tr.storeEntry').each(function() {
        var element = $(this);
        element.find('td.dataCell').empty();
        element.addClass('loading');
        $.ajax({
            url: '/analytic/dashboard_feed_new.php',
            type: 'GET',
            data: {
                token: token,
                startdate: start_date, enddate: end_date,
                Storeid: element.attr('data-member'),
                optimized: 1, adjust_traffic: 1, norollups: 0,
                nocache: 0, parameter: 'Get Dashboard Data'
            },
            success: function(data) {
                var result = {};
                result['cI'] = data;
                $.ajax({
                    url: '/analytic/dashboard_feed_new.php',
                    type: 'GET',
                    data: {
                        token: token,
                        startdate: start_date_2, enddate: end_date_2,
                        Storeid: element.attr('data-member'),
                        optimized: 1, adjust_traffic: 1, norollups: 0,
                        nocache: 0, parameter: 'Get Dashboard Data'
                    },
                    success: function(data) {
                        result['lI'] = data;
                        if ((typeof result['cI'].error == 'undefined') && (typeof result['lI'].error == 'undefined')) {
                            store_num++;
                            r = {cI: result['cI'].Totals, lI: result['lI'].Totals};
                            r['cI']['AvgTicket'] = parseFloat(r['cI']['AvgTicket']);
                            r['lI']['AvgTicket'] = parseFloat(r['lI']['AvgTicket']);
                            var schema = [
                                {selector: 'Walkbys', index: 'Walkbys', avg: false, currency: false, percentage: false, time: false},
                                {selector: 'TotalShoppers', index: 'TotalShoppers', avg: false, currency: false, percentage: false, time: false},
                                {selector: 'Transactions', index: 'Transactions', avg: false, currency: false, percentage: false, time: false},
                                {selector: 'ReturningShoppers', index: 'ReturnVisitors', avg: false, currency: false, percentage: false, time: false},
                                {selector: 'AvgDwell', index: 'DwellTime', avg: true, currency: false, percentage: false, time: true},
                                {selector: 'AvgTicket', index: 'AvgTicket', avg: true, currency: true, percentage: false, time: false},
                                {selector: 'ItemsPerTransaction', index: 'ItemsPerTransaction', avg: true, currency: false, percentage: false, time: false},
                                {selector: 'Revenue', index: 'Revenue', avg: false, currency: true, percentage: false, time: false},
                                {selector: 'ConversionRate', index: 'ConversionRate', avg: true, currency: false, percentage: true, time: false},
                                {selector: 'WindowConversion', index: 'WindowConversion', avg: true, currency: false, percentage: true, time: false}
                            ];
                            schema.forEach(function(i) {
                                var cur = (i.currency) ? currency : '';
                                var per = (i.percentage) ? '%' : '';
                                element.find('.' + i.selector).html(cur + addCommas(r['cI'][i.index]) + per).attr('data-sorter', r['cI'][i.index]);
                                calculatePercentage(r['cI'][i.index], r['lI'][i.index], element.find('.' + i.selector), true, true, false, i.currency);
                                if (typeof totals['cI'][i.index] == 'undefined') {
                                    totals['cI'][i.index] = 0;
                                    totals['lI'][i.index] = 0;
                                }
                                if (i.time) {
                                    var tmp = ['cI', 'lI'];
                                    tmp.forEach(function(j) {
                                        var hms = r[j][i.index].split(':');
                                        totals[j][i.index] += parseInt(hms[0]) * 3600;
                                        totals[j][i.index] += parseInt(hms[1]) * 60;
                                        totals[j][i.index] += parseInt(hms[2]);
                                    });
                                } else {
                                    totals['cI'][i.index] += r['cI'][i.index];
                                    totals['lI'][i.index] += r['lI'][i.index];
                                }
                            });
                            schema.forEach(function(i) {
                                var cur = (i.currency) ? currency : '';
                                var per = (i.percentage) ? '%' : '';
                                var num = (i.avg) ? store_num : 1;
                                var fixed = (i.percentage || i.currency || i.avg) ? 2 : 0;
                                if (i.time) {
                                    $('tr.storeEntryTotal').find('.' + i.selector).html(cur + addCommas((makeHMS((totals['cI'][i.index] / num).toFixed(fixed)))) + per).attr('data-sorter', totals['cI'][i.index]);
                                } else {
                                    $('tr.storeEntryTotal').find('.' + i.selector).html(cur + addCommas((totals['cI'][i.index] / num).toFixed(fixed)) + per).attr('data-sorter', totals['cI'][i.index]);
                                }
                                calculatePercentage(totals['cI'][i.index] / num, totals['lI'][i.index] / num, $('tr.storeEntryTotal').find('.' + i.selector), true, true, false, i.currency);
                                $('tr.storeEntryTotal').removeClass('loading');
                            });
                        }
                        element.removeClass('loading');
                        $("#AllStores").trigger("update");
                    }
                });
            }
        });
    });
}

function setReportTitle(start, end, start_2, end_2) {
    if (start.getTime() === end.getTime()) {
        $('.varATitle').html(start.toString('dddd MMMM dS, yyyy'));
        $('#range_a span').html(start.toString('MMMM dS, yyyy'));
    } else {
        $('#range_a span, .varATitle').html(start.toString('MMMM dS, yyyy') + ' - ' + end.toString('MMMM dS, yyyy'));
    }
    if (start_2.getTime() === end_2.getTime()) {
        $('.varBTitle').html(start_2.toString('dddd MMMM dS, yyyy'));
    } else {
        $('.varBTitle').html(start_2.toString('dddd MMMM dS, yyyy') + ' - ' + end_2.toString('dddd MMMM dS, yyyy'));
    }
}

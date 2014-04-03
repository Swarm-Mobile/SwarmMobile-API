
global_data = 'There is no data to display.';
comparison_data = 'There is no data to display.';

$(document).ready(function() {

    refreshStoreAnalytics();

    //check for existing user preferences dashboard cookie
    var store_analytics_filters = JSON.parse($.cookie('store_analytics_filters'));
    //if we have a cookie, deal with them
    if (store_analytics_filters != null) {
        setCookieFilters(store_analytics_filters, 'hide', 'store_analytics_filters');
        //if not, pass empty so we use defaults
    } else {
        store_analytics_filters = [
            {Walkbys: 1},
            {WindowConversion: 0},
            {TotalShoppers: 1},
            {ReturningShoppers: 0},
            {Transactions: 1},
            {AvgDwell: 0},
            {ConversionRate: 0},
            {Revenue: 1},
            {AvgTicket: 0},
            {ItemsPerTransaction: 0}
        ];
        setCookieFilters(store_analytics_filters, 'hide', 'store_analytics_filters');
    }
    //when clicking metrics from dashboard, check to see if they are hidden, if they are- show them and change the preference cookie, scroll to that div
    var hash = window.location.hash.substr(1);
    if (hash) {
        $('.' + hash).show().removeClass('hide');
        var theCheckboxPreference = $('.userPreferencesFieldset').find('input[name="' + hash + '"]');
        console.log(theCheckboxPreference);
        if (theCheckboxPreference.is(':checked')) {
            console.log('thing is checked');
        } else {
            console.log('not checked, do something')
            document.getElementById(hash).scrollIntoView();
            theCheckboxPreference.click();
        }
    }
    ;



//define range pickers with options and defautls
    $('#range_a').daterangepicker(rangepicker_options,
            function(start, end) {
                start = start.toDate();
                end = end.toDate();

                var start_date = start.toString('MMMM dS, yyyy');
                var end_date = end.toString('MMMM dS, yyyy');

//get days between and weeks between
                var range_of_days = days_between(start, end) + 1;
                var range_of_weeks = weeks_between(start, end) + 1;
//go back X numbers of weeks and then forward by the range of days being looked at
                var days_to_go_back = range_of_weeks * 7;
                var days_to_go_forward = (days_to_go_back - range_of_days) + 1;

                if (range_of_days == 1) {
                    start_range_2 = Date.parse(start_date).add({days: -7});
                    end_range_2 = Date.parse(start_date).add({days: -7});
                } else {
                    start_range_2 = Date.parse(start_date).add({days: -days_to_go_back});
                    end_range_2 = Date.parse(start_date).add({days: -days_to_go_forward});
                }

                var start_range_2_pretty = start_range_2.toString('dddd MMMM dS, yyyy');
                var end_range_2_pretty = end_range_2.toString('dddd MMMM dS, yyyy');


                var db_start = start.toString('yyyy-MM-dd');
                var db_end = end.toString('yyyy-MM-dd');

                var db_start2 = start_range_2.toString('yyyy-MM-dd');
                var db_end2 = end_range_2.toString('yyyy-MM-dd');

                $.cookie('start_range_1', db_start, {path: '/'});
                $.cookie('end_range_1', db_end, {path: '/'});

                start_range_1 = db_start;
                end_range_1 = db_end;



// getStoreAnalyticsData('varA');
                getStoreAnalyticsData(start_range_1, end_range_1, 'varA');
                getStoreAnalyticsData(db_start2, db_end2, 'varB');
                setReportTitle(start, end, start_range_2, end_range_2);

            }
    );

    $('#csvButton').on('click', function(e) {
        e.preventDefault();
        window.open('/analytic/dashboard_feed_csv.php?token=' + token + '&startdate=' + start_range_1 + '&enddate=' + end_range_1 + '&Storeid=' + member_id);
    });

    $('#refreshButton').on('click', function() {
        refreshStoreAnalytics();
    });

    $('.drilldownTrigger').on('click', function(e) {
        e.preventDefault();
        var el = $(this).parent('.metric');
        showDrillDown(el);
    });

});


function getStoreAnalyticsData(start_date, end_date, placement) {
    $('.graph').fadeOut();

    if (placement == 'varA') {

        // chart.showLoading();

    }
    ;


    $('.progress-bar').css('width', 0);
    var requestCallback = new MyRequestsCompleted({
        numRequest: 2,
        singleCallback: function() {
        }
    });
//hide existing range data
// $('.'+placement).html('');
    showDashboardLoading();
    $('.metric .change,.insight .change').empty();


    var data_set;

    var return_visits = $.ajax({
        url: "/analytic/store_analytics.php",
// 
        data: {
            'token':token,
            'startdate': start_date,
            'enddate': end_date,
            'Storeid': member_id,
            'optimized': 1,
            'adjust_traffic': 1,
            'norollups': no_rollups,
            'nocache': no_cache,
            'parameter': 'Get Dashboard Data'},
        dataType: "json",
        cache: true,
        type: "GET",
        success: function(data) {
            // console.log(data);
            data_set = data;
            if (data_set != 'No data found') {
                if (placement == 'varA') {
                    global_data = data;

                }
                ;
                if (placement == 'varB') {
                    comparison_data = data;
                }
                ;

//
// SET METRICS
//
                range_total_shoppers = data.Totals.TotalShoppers;
                range_returning_shoppers = data.Totals.ReturnVisitors;
                range_total_walkbys = data.Totals.Walkbys;
                if (range_total_walkbys) {
                    range_total_window_conversion = ((data.Totals.TotalShoppers / (data.Totals.Walkbys + data.Totals.TotalShoppers)) * 100).toFixed(2);
                } else {
                    range_total_window_conversion = 0;
                }

                if (range_total_window_conversion > 100) {
                    range_total_window_conversion = 100;
                }
                if (!range_total_window_conversion || isNaN(range_total_window_conversion)) {
                    range_total_window_conversion = 0;
                }
                ;

                range_avg_dwell = data.Totals.DwellTime;
                range_avg_dwell_seconds = splitHMS(range_avg_dwell);
                range_app_use = data.Totals.AppUse;
                range_transactions = data.Totals.Transactions;
                range_conversion = data.Totals.ConversionRate;
                range_avgticket = data.Totals.AvgTicket;
                range_revenue = parseInt(data.Totals.Revenue);
                range_itemspertransaction = data.Totals.ItemsPerTransaction;

                $('.TotalShoppers .' + placement).html(addCommas(range_total_shoppers)).attr('data-metric', range_total_shoppers);
                $('.ReturningShoppers .' + placement).html(addCommas(range_returning_shoppers)).attr('data-metric', range_returning_shoppers);
                $('.Walkbys .' + placement).html(addCommas(range_total_walkbys)).attr('data-metric', range_total_walkbys);
                $('.WindowConversion .' + placement).html(addCommas(range_total_window_conversion) + '%').attr('data-metric', range_total_window_conversion);
                $('.AvgDwell .' + placement).html(range_avg_dwell).attr({'data-metric': range_avg_dwell, 'data-seconds': range_avg_dwell_seconds});
                $('.AppUse .' + placement).html(addCommas(range_app_use) + '%').attr('data-metric', range_app_use);
                if (lightspeed_id != "0") {
                    $('.Revenue .' + placement).html(currency + addCommas(range_revenue)).attr('data-metric', range_revenue);
                    $('.Transactions .' + placement).html(addCommas(range_transactions)).attr('data-metric', range_transactions);
                    $('.ConversionRate .' + placement).html(addCommas(range_conversion) + '%').attr('data-metric', range_conversion);
                    $('.AvgTicket .' + placement).html(currency + addCommas(range_avgticket)).attr('data-metric', range_avgticket);
                    $('.ItemsPerTransaction .' + placement).html(addCommas(range_itemspertransaction)).attr('data-metric', range_itemspertransaction);
                }
                if (start_range_2 == '' && end_range_2 == '') {
                    $('.change,.varB').hide();
                } else {
                    $('.change,.varB').fadeIn();
                    if (placement == 'varB') {
                        $('.varB').prepend('vs. ');
                    }
                }
                requestCallback.requestComplete(true);


// stopLoading();

            }
            ;
        }
    });

}
var MyRequestsCompleted = (function() {
    var numRequestToComplete,
            requestsCompleted,
            callBacks,
            singleCallBack;

    return function(options) {
        if (!options)
            options = {};

        numRequestToComplete = options.numRequest || 0;
        requestsCompleted = options.requestsCompleted || 0;
        callBacks = [];
        var fireCallbacks = function() {
            calcRangeChanges();
            drawGraphs(global_data, comparison_data);

            for (var i = 0; i < callBacks.length; i++)
                callBacks[i]();
        };
        if (options.singleCallback)
            callBacks.push(options.singleCallback);



        this.addCallbackToQueue = function(isComplete, callback) {
            if (isComplete)
                requestsCompleted++;
            if (callback)
                callBacks.push(callback);
            if (requestsCompleted == numRequestToComplete)
                fireCallbacks();
        };
        this.requestComplete = function(isComplete) {
            if (isComplete)
                requestsCompleted++;
            if (requestsCompleted == numRequestToComplete)
                fireCallbacks();
        };
        this.setCallback = function(callback) {
            callBacks.push(callBack);
        };
    };
})();
//calc change row when new data is loaded
function calcRangeChanges() {
    $('.metric,.insight').each(function() {
        if ($(this).hasClass('dataRowTitles')) {
            return;
        }


        if ($(this).hasClass('AvgDwell')) {

            var_one = $(this).find('.varA').attr('data-seconds');
            var_two = $(this).find('.varB').attr('data-seconds');
        } else {

            var_one = $(this).find('.varA').attr('data-metric');
            var_two = $(this).find('.varB').attr('data-metric');

        }
        ;

        calculatePercentage(var_one, var_two, $(this).find('.change'), false, false, true, currency);


    })
}
;

function refreshStoreAnalytics() {
    getStoreAnalyticsData(start_range_1, end_range_1, 'varA');
    getStoreAnalyticsData(start_range_2, end_range_2, 'varB');
}
;

function showDashboardLoading() {
    $('.focusNumber .varA, .secondaryNumber').html('<img src="/b2b/images/ajax-loader.gif"/>');
}

/* chart stuff */

//chart options
var tick;
var chartDefaults = {
    chartContent: null,
    highchart: null,
    defaults: {
        chart: {
            // height: 300,
            renderTo: null,
            type: 'areaspline',
            backgroundColor: '#fff',
            borderColor: '#e9eff0',
            spacingLeft: 30
        },
        colors: [
            '#3498db',
            '#e9eff0',
            '#fd7037',
            '#f39c12',
            '#8e44ad'
        ],
        credits: {
            'enabled': false
        },
        legend: {
            verticalAlign: 'top',
            backgroundColor: '#fff',
            borderWidth: 0,
            itemStyle: {
                cursor: 'pointer',
                color: '#1b2224',
                fontSize: '13px',
                fontFamily: 'proxima_nova_rgregular'
            }
        },
        tooltip: {
            useHtml: true,
            shared: true,
            formatter: function() {
                var s = '<b>' + this.points[0].point.category + '</b><br/>';
                $.each(this.points, function(i, point) {
                    if (this.series.name == "Comparison Range") {
                        s += '<br/><b>' + comparison_data.Categories[i] + '</b>';
                    }
                    ;
                    s += '<br/><span style="color:' + point.series.color + ';font-weight:bold;padding: 3px 0; display:block;">' + point.series.tooltipOptions.valuePrefix + addCommas(point.y) + point.series.tooltipOptions.valueSuffix + '</span>';
                });
                return s;
            }
        },
        plotOptions: {
            allowPointSelect: true,
            series: {
                cursor: 'pointer',
                fillOpacity: 0.5,
                lineWidth: 2,
                // stacking: 'normal',
                marker: {
                    symbol: 'circle',
                    fillColor: '#fff',
                    lineWidth: 2,
                    radius: 4,
                    lineColor: null // inherit from series
                }
            }
        },
        title: {
            text: ''
        },
        xAxis: {},
        yAxis: [{
                min: 0,
                allowDecimals: false,
                gridLineColor: '#e9eff0',
                lineColor: '#e9eff0',
                lineWidth: 1,
                title: {
                    text: '',
                    style: {
                        color: '#1b2224',
                        fontWeight: 'bold'
                    }
                },
                labels: {
                    overflow: 'justify',
                    align: 'left',
                    style: {
                        color: '#1b2224',
                        fontWeight: 'bold'
                    },
                    x: -30,
                    formatter: function() {
                        if (this.chart.renderTo.id == "DwellBreakdown_chart") {
                            return makeHMS(this.value);
                        } else {
                            return this.value;
                        }
                    }
                }
            }]


    },
    init: function(options) {

        this.highchart = jQuery.extend({}, this.defaults, options);
        this.highchart.chart.renderTo = this.chartContent;
    },
    create: function() {

        new Highcharts.Chart(this.highchart);
    }

};

function drawGraphs(graph_data, comparison_graph_data) {
    $('.graph').fadeIn();
    $('.metric').each(function() {
        var metric = $(this).data('metric');
        var color = $(this).data('color');


        var suffix = '';
        var prefix = '';
        if ($(this).data('tooltip-suffix')) {
            suffix = $(this).data('tooltip-suffix');
        }
        if ($(this).data('tooltip-prefix')) {
            prefix = $(this).data('tooltip-prefix');
        }

        var total_points = graph_data.Categories.length;
        if (total_points > 10) {
            graphTickInterval = Math.floor(total_points / 4);
        } else {
            graphTickInterval = 1;
        }

        var metricChart = {
            chartContent: metric + '_chart',
            options: {
                colors: [
                    '#' + color,
                    '#4e5c60'
                ],
                xAxis: [
                    {
                        categories: graph_data.Categories,
                        tickPosition: 'outside',
                        tickmarkPlacement: 'on',
                        tickInterval: graphTickInterval
                    }
                ],
                series: [
                    {
                        name: (global_date_start == global_date_end ? global_date_start : (global_date_start + ' - ' + global_date_end)),
                        data: graph_data[metric],
                        zIndex: 10,
                        tooltip: {
                            valueSuffix: suffix,
                            valuePrefix: prefix
                        }
                    }, {
                        name: (global_date_start2 == global_date_end2 ? global_date_start2 : (global_date_start2 + ' - ' + global_date_end2)),
                        type: 'spline',
                        data: comparison_data[metric],
                        zIndex: 0,
                        tooltip: {
                            valueSuffix: suffix,
                            valuePrefix: prefix
                        }
                    }]
            }
        };

        metricChart = jQuery.extend(true, {}, chartDefaults, metricChart);
        metricChart.init(metricChart.options);
        metricChart.create();
    });

}
/*
 Shows Drilldowns for a given metric
 */
function showDrillDown(el, dataArray, placement) {
    var drilldown = el.parent('.row').find('.drilldown');

    // if already open, close
    if (el.hasClass('active')) {
        el.removeClass('active');
        drilldown.find('.drilldownTarget').empty();
        drilldown.hide();
        return false;
    } else {
        el.addClass('active');
    }

    //get range data for comparison and primary
    var metric = el.data('title');
    var primaryRangeData = getDrillDownData(global_data, metric);
    var comparisonRangeData = getDrillDownData(comparison_data, metric);

    //add data to html, show
    drilldown.find('.global').html(primaryRangeData);
    drilldown.find('.comparison').html(comparisonRangeData);
    drilldown.slideDown();


}

function getDrillDownData(dataArray, metric) {
    var cat_length = dataArray.Categories.length;
    var inStr = '';

    for (var i = 0; i < cat_length; i++) {
        if (dataArray.Categories[i] == 'Open' || dataArray.Categories[i] == 'Close') {

        } else {
            if (metric == 'TotalShoppers') {
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + (dataArray.FootTraffic[i].y ? dataArray.FootTraffic[i].y : '0') + '</span></p>';
            }
            if (metric == 'ReturningShoppers') {
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + (dataArray.ReturningVisitors[i] ? dataArray.ReturningVisitors[i] : '0') + '</span></p>';
            }
            if (metric == 'Walkbys') {
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + (dataArray.Walkbys[i] ? dataArray.Walkbys[i] : '0') + '</span></p>';
            }
            if (metric == 'WindowConversion') {
                var window_conversion = ((dataArray.FootTraffic[i].y / dataArray.Walkbys[i]) * 100).toFixed(2);
                if (isNaN(window_conversion) || !isFinite(window_conversion)) {
                    window_conversion = 0;
                }
                ;
                if (window_conversion > 100) {
                    window_conversion = 100;
                }
                ;
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + (window_conversion ? window_conversion : '0') + '%</span></p>';
            }

            if (metric == 'AvgDwell') {
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + (dataArray.AvgDwell[i] ? dataArray.AvgDwell[i] : '0') + '</span></p>';
            }

            if (metric == 'Revenue') {
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + currency + (dataArray.Revenue[i] ? dataArray.Revenue[i] : '0') + '</span></p>';
            }

            if (metric == 'Transactions') {
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + (dataArray.Transactions[i] ? dataArray.Transactions[i] : '0') + '</span></p>';
            }

            if (metric == 'ItemsPerTransaction') {
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + (dataArray.ItemsPerTransaction[i] ? dataArray.ItemsPerTransaction[i] : '0') + '</span></p>';
            }

            if (metric == 'ConversionRate') {
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + (dataArray.ConversionRate[i] ? dataArray.ConversionRate[i] : '0') + '%</span></p>';
            }

            if (metric == 'AvgTicket') {
                inStr += '<p><span class="primaryBold metricTitle">' + dataArray.Categories[i] + ':</span><span class="metricData pull-right"> ' + currency + (dataArray.AvgTicket[i] ? dataArray.AvgTicket[i] : '0') + '</span></p>';
            }
        }
    }
    ;
    return inStr;
}

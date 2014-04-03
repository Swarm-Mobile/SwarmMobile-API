var tick;

global_data = 'There is no data to display.';

$(document).ready(function() {

    refreshDashboard();

    //check for existing user preferences dashboard cookie
    var dashboard_filters = JSON.parse($.cookie('dashboard_filters'));
    //if we have a cookie, deal with them
    if (dashboard_filters != null) {
        setCookieFilters(dashboard_filters);
        //if not, pass empty so we use defaults
    } else {
        setCookieFilters();
    }






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



                getDashboardData(start_range_1, end_range_1, 'varA');
                getDashboardData(db_start2, db_end2, 'varB');
                setReportTitle(start, end, start_range_2, end_range_2);

                if (!isAdmin) {
                    mixpanel.track("Dashboard Dates Changed");
                }

            }
    );

    $('#csvButton').on('click', function(e) {
        e.preventDefault();
        window.open('/analytic/dashboard_feed_csv.php?token=' + token + '&startdate=' + start_range_1 + '&enddate=' + end_range_1 + '&Storeid=' + member_id);
    });

    $('#refreshButton').on('click', function() {
        refreshDashboard();
    });

});

//chart options
var chart = new Highcharts.Chart({
    chart: {
        renderTo: 'avc',
        type: 'areaspline',
        backgroundColor: '#fff',
        borderColor: '#e9eff0'
    },
    colors: [
        '#3498db',
        '#27ae60',
        '#fd7037',
        '#4e5c60'
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
            var s = '<b>' + this.points[0].point.category + '</b>';
            $.each(this.points, function(i, point) {
                s += '<br/><span style="color:' + point.series.color + ';font-weight:bold;padding: 3px 0; display:block;">' + point.series.name + ': ' + point.series.tooltipOptions.valuePrefix + point.y + point.series.tooltipOptions.valueSuffix + '</span>';
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
            marker: {
                fillColor: '#fff',
                symbol: 'circle',
                lineWidth: 2,
                radius: 4,
                lineColor: null // inherit from series
            }
        }
    },
    title: {
        text: ''
    },
    xAxis: [{
            categories: [],
            endOnTick: false,
            tickPosition: 'inside',
            tickmarkPlacement: 'on',
            tickInterval: tick,
            lineColor: '#e9eff0',
            lineWidth: 1,
            tickColor: '#e9eff0',
            title: {
                text: 'Store Hours',
                style: {
                    color: '#1b2224',
                    fontWeight: 'bold'
                }
            }
        }],
    yAxis: [{
            min: 0,
            allowDecimals: false,
            gridLineColor: '#e9eff0',
            lineColor: '#e9eff0',
            lineWidth: 1,
            title: {
                text: 'Shoppers',
                style: {
                    color: '#1b2224',
                    fontWeight: 'bold'
                }
            }
        },
        {
            //revenue axis
            opposite: true,
            min: 0,
            allowDecimals: false,
            gridLineColor: '#e9eff0',
            lineColor: '#e9eff0',
            gridLineWidth: 0,
            lineWidth: 1,
            labels: {
                formatter: function() {
                    return currency + this.value;
                },
                style: {
                    color: '#1b2224'
                }
            },
            title: {
                text: 'Revenue',
                style: {
                    color: '#1b2224',
                    fontWeight: 'bold'
                }
            }
        }],
    series: [
        {
            name: 'Walkbys',
            visible: true,
            yAxis: 0,
            lineWidth: 2,
            tooltip: {
                valuePrefix: '',
                valueSuffix: ''
            },
            data: []
        },
        {
            name: 'Foot Traffic',
            yAxis: 0,
            lineWidth: 2,
            tooltip: {
                valuePrefix: '',
                valueSuffix: ''
            },
            data: []
        },
        {
            name: 'Transactions',
            visible: true,
            yAxis: 0,
            data: [],
            tooltip: {
                valuePrefix: '',
                valueSuffix: ''
            }
        },
        {
            name: 'Revenue',
            visible: false,
            yAxis: 1,
            type: 'spline',
            lineWidth: 2,
            marker: {
                symbol: 'circle',
                fillColor: '#4e5c60'
            },
            tooltip: {
                valuePrefix: currency,
                valueSuffix: ''
            },
            data: []
        }
        // {
        //   name: 'Sensor Traffic',
        //   visible: false,
        //   yAxis: 0,
        //   marker:{
        //     symbol:'circle',
        //     // lineColor:'#34495e',
        //     // fillColor:'#34495e',
        //     lineWidth:1,
        //     radius: 2
        //   },
        //   lineWidth:2,
        //   tooltip: {
        //     valuePrefix: '',
        //     valueSuffix: ''
        //   },
        //   data: []
        // }
    ]
});
// end chart data


function refreshDashboard() {
    getDashboardData(start_range_1, end_range_1, 'varA');
    getDashboardData(start_range_2, end_range_2, 'varB');
}
;
function getDashboardData(start_date, end_date, placement) {
    if (placement == 'varA') {

        chart.showLoading();

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

    var dashboard_call = $.ajax({
        url: "/analytic/dashboard_feed_new.php",
        data: {
            'token': token,
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
                    var total_points = data_set.Categories.length;
                    chart.series[0].setData(data_set.Walkbys);
                    chart.series[1].setData(data_set.FootTraffic);
                    chart.series[2].setData(data_set.Transactions);
                    chart.series[3].setData(data_set.Revenue);
                    // chart.series[5].setData(data_set.SensorTraffic);
                    chart.xAxis[0].setCategories(data_set.Categories);
                    chart.hideLoading();

                    if (total_points > 20) {
                        chart.xAxis[0].options.tickInterval = Math.floor(total_points / 4);
                    } else {
                        chart.xAxis[0].options.tickInterval = 1;
                    }

                    if (total_points = 3) {
                        // storeClosed();
                    }

                    chart.xAxis[0].isDirty = true;
                    chart.redraw();
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



function showDashboardLoading() {
    $('.focusNumber .varA, .secondaryNumber').html('<img src="/b2b/images/ajax-loader.gif"/>');
}





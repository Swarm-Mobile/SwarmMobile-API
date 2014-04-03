var current_data = 'There is no data to display';
var comparison_data = 'There is no data to display';
var loadedEmail  = null;
var loadedDomain  = null
var email_table_data = Array();
var website_table_data = Array();

$(document).ready(function() {
    // varA = user selected, varB = previous range for comparison
    refreshNetworkAnalytics();
    // Define date range picker
    $('#range_a').daterangepicker(rangepicker_options, function (start, end) {
    	// start and end are moment objects, convert them to date objects
        start = start.toDate();
        end   = end.toDate();

        // e.g. March 12th, 2014
        var start_date = start.toString('MMMM dS, yyyy');
        var end_date   = end.toString('MMMM dS, yyyy');

        // Get days between and weeks between the range
        var range_of_days  = days_between(start, end) + 1;
        var range_of_weeks = weeks_between(start, end) + 1;

        // Go back X numbers of weeks and then forward by the range of days being looked at
        var days_to_go_back = range_of_weeks * 7;
        var days_to_go_forward = (days_to_go_back - range_of_days) + 1;

        if (range_of_days == 1) {
        	start_range_2  = Date.parse(start_date).add({ days: -7 });
        	end_range_2    = Date.parse(start_date).add({ days: -7 });
        } else {
        	start_range_2  = Date.parse(start_date).add({ days: -days_to_go_back});
        	end_range_2    = Date.parse(start_date).add({ days: -days_to_go_forward});
        }

        var start_range_2_pretty = start_range_2.toString('dddd MMMM dS, yyyy');
        var end_range_2_pretty   = start_range_2.toString('dddd MMMM dS, yyyy');

        // Date format to send to the backend
        var db_start = start.toString('yyyy-MM-dd');
        var db_end   = end.toString('yyyy-MM-dd');

        var db_start_2 = start_range_2.toString('yyyy-MM-dd');
        var db_end_2   = end_range_2.toString('yyyy-MM-dd');

        $.cookie('start_range_1', db_start, {path: '/'});
        $.cookie('end_range_1', db_end, {path: '/'});

        start_range_1 = db_start;
        end_range_1 = db_end;

        // Get data for the new picked range
        getData(db_start, db_end, 'varA');
        getData(db_start_2, db_end_2, 'varB');
        setReportTitle(start, end, start_range_2, end_range_2);

        if(!isAdmin){
            mixpanel.track("Network Analytics Dates Changed");
        }
    });
    $('#refreshButton').on('click', function(){
      refreshNetworkAnalytics();
    });
});
/**
 * Refreshes data on Network Analytics
 */
function refreshNetworkAnalytics(){
    getData(start_range_1, end_range_1, 'varA');
    getData(start_range_2, end_range_2, 'varB');
}
/**
 * Ajax call to get network data
 *
 * @param String start date
 * @param String end date
 */
function getData(start_date, end_date, placement) {
    $('.graph').fadeOut();

    $('.progress-bar').css('width', 0);

    var requestCallback = new MyRequestsCompleted({
    	numRequest: 2,
    	singleCallback: function() {}
    });

    showDashboardLoading();
    $('.metric .change, .insight .change').empty();

    var data_set;

    var return_visits = $.ajax({
    	url: '/analytic/ajax/Network_Controller.php',
    	data: {
                'token':token,
    		'startDate': start_date,
    		'endDate': end_date,
    		'storeId': member_id,
    		'optimized': 1,
    		'noCache': no_cache,
    	},
    	dataType: 'json',
    	cache: true,
    	type: 'POST',
    	success: function(data) {
            //console.log(placement);
    		//console.log(data);
    		data_set = data;
    		if (data_set.error != 1) {
    			switch(placement) {
    				case 'varA' :
    				    current_data = data;
                        var email_count = data.emails_captured.length;
                        var emails = data.emails_captured;
                        var website_count = data.domains.length;
                        var websites = data.domains;
                        $('.website_list tbody').html('');
                        $('.email_list tbody').html('');

                        if (email_count >= 1) {

                            email_table_data = $.merge(email_table_data, emails);
                        }
                        
                        if (website_count >= 1) {
                            website_table_data = $.merge(website_table_data, websites);

                        } 

    				    break;

    				case 'varB':
    				    comparison_data = data;
                        var email_count = data.emails_captured.length;
                        var emails = data.emails_captured;
                        var website_count = data.domains.length;
                        var websites = data.domains;
                        if (email_count >= 1) {
                           // email_table_data = $.merge(email_table_data, emails);
                        }

                        if (website_count >= 1) {
                            // website_data_table = $.merge(website_table_data, websites);
                        }

    			     break;

                     default:
    				    break;
    			}

                $('.connections .'+ placement).html(addCommas(data.totals.connections)).attr('data-metric', data.totals.connections);
                $('.emails .'+ placement).html(addCommas(data.totals.emails)).attr('data-metric', data.totals.emails);

                if(start_range_2 == '' && end_range_2 == ''){
                     $('.change,.varB').hide();
                } else {
                    $('.change,.varB').fadeIn();
                    if(placement=='varB'){
                         $('.varB').prepend('vs. ');
                    }
                }
    		    // Set metrics
                requestCallback.requestComplete(true);
    		}
    	}
    });
}


var MyRequestsCompleted = (function() {
    var numRequestToComplete, requestsCompleted, callBacks, singleCallBack;

    return function(options) {
        if (!options) options = {};

        numRequestToComplete = options.numRequest || 0;
        requestsCompleted    = options.requestsCompleted || 0;
        callBacks = [];

        var fireCallbacks = function() {
        	calcRangeChanges();
        	drawGraphs(current_data, comparison_data);

        	for (var i = 0; i < callBacks.length; i++) 
        		callBacks[i]();

            if (loadedEmail != null) {loadedEmail.fnDestroy(); $('#tableLoadEmail tbody').empty(); loadedEmail = null;}

            if (email_table_data.length > 0) {
                for(var i=0; i < email_table_data.length; i++) {
                   $('.email_list table tbody').append('<tr><td class="linked">' + email_table_data[i]['email'] + '</td><td>' + email_table_data[i]['time_seen'] + '</td></tr>');
                }

                loadedEmail = $('#nw_email_list').dataTable({
                    "aaSorting":[[1,"desc"]],
                    "bFilter":false,
                    "aLengthMenu": [[10, 25, 50, 100, -1],[10, 25, 50, 100, "All"]],
                    "sPaginationType": "bootstrap",
                    "sDom": '<"top">rt<"bottom"filp><"clear">',
                    bRetrieve: true
                });
            } else {
                $('.email_list table tbody').html('<tr><td colspan="2" class="text-center tableEmpty">There is no email data available from these dates.</td></tr>');
            }

            email_table_data = [];

            if (loadedDomain != null) {loadedDomain.fnDestroy(); $('#tableLoadWebsite tbody').empty(); loadedDomain = null;}
            if (website_table_data.length > 0) {
                for(var i=0; i < website_table_data.length; i++) {
                    $('.website_list table tbody').append('<tr><td>' + website_table_data[i]['domain'] + '</td><td>' + website_table_data[i]['count'] + '</td></tr>');
                }
                loadedDomain = $('#nw_website_list').dataTable({
                    "aaSorting":[[1,"desc"]],
                    "bFilter":false,
                    "aLengthMenu": [[10, 25, 50, 100, -1],[10, 25, 50, 100, "All"]],
                    "sPaginationType": "bootstrap",
                    "sDom": '<"top">rt<"bottom"filp><"clear">',
                    bRetrieve: true
                });
            } else {
                $('.website_list table tbody').html('<tr><td colspan="2" class="text-center tableEmpty">There is no browsing data available from these dates.</td></tr>');
            }

            website_table_data = [];
        };

        if (options.singleCallback) callBacks.push(options.singleCallback);

        this.addCallbackToQueue = function(isComplete, callback) {
        	if (isComplete) requestsCompleted++;
        	if (callback) callBacks.push(callback);
        	if (requestsCompleted == numRequestToComplete) fireCallbacks(); 
        };

        this.requestComplete = function(isComplete) {
            if (isComplete) requestsCompleted++;
            if (requestsCompleted == numRequestToComplete) fireCallbacks();
        };

        this.setCallback = function(callback) {
            callBacks.push(callBack);
        };

    }
})();

/**
 * Data loading gif
 */
function showDashboardLoading() {
    $('.focusNumber .varA, .secondaryNumber').html('<img src="/b2b/images/ajax-loader.gif"/>');
}

/**
 * Calculate percentage change between current and 
 * previous periods
 */
 function calcRangeChanges() {
 	$('.metric, .insight').each(function() {
 		if ($(this).hasClass('dataRowTitles')) return;

 		var current_metric = $(this).find('.varA').attr('data-metric');
 		var compare_metric    = $(this).find('.varB').attr('data-metric');
 		calculatePercentage(current_metric, compare_metric, $(this).find('.change'), false, false, true, currency);
 	});
 }


// Download data as a CSV file
/*$('#csvButton').on('click', function (e) {
	e.preventDefault();
	// To do
	window.open('/analytic/dashboard_feed_csv.php?startdate='+start_range_1+'&enddate='+end_range_1+'&Storeid='+member_id);
});*/

/* Charts */
var tick;

var chart_defaults = {
	chartContent: null,
	highchart: null,
	defaults: {
		chart: {
			renderTo: null,
			type: 'areaspline',
			backgroundColor: '#fff',
			borderColor: '#e9eff0',
			spacingLeft: 30
		},
		colors :[
            '#3498db',
            '#e9eff0',
            '#fd7037',
            '#f39c12',
            '#8e44ad'
        ],
        credits: {
            'enabled' : false
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
                var s = '<b>'+ this.points[0].point.category +'</b><br/>';
                $.each(this.points, function(i, point) {
                    if(this.series.name=="Comparison Range"){
                        s+= '<br/><b>'+comparison_data.xIntervals[i]+'</b>';
                    };
                    s += '<br/><span style="color:'+point.series.color+';font-weight:bold;padding: 3px 0; display:block;">'+point.series.tooltipOptions.valuePrefix+addCommas(point.y)+point.series.tooltipOptions.valueSuffix+'</span>';
                });
                return s;
            }
        },
        plotOptions: {
            allowPointSelect: true,
            series: {
                cursor: 'pointer',
                fillOpacity: 0.7,
                lineWidth: 2,
                marker: {
                    symbol:'circle',
                    fillColor: '#fff',
                    lineWidth: 2,
                    radius: 4,
                    lineColor: null // inherit from series
                }
            }
        },
        title:{
            text: ''
        },
        xAxis: {},
        yAxis:[{
            min: 0,
            allowDecimals: false,
            gridLineColor: '#e9eff0',
            lineColor: '#e9eff0',
            lineWidth: 1,
            title:{
                text: '',
                style : {
                    color: '#1b2224',
                    fontWeight: 'bold'
                }
            },
            labels: {
                overflow: 'justify',
                align: 'left',
                style : {
                    color: '#1b2224',
                    fontWeight: 'bold'
                },
                x: -30,
                formatter: function() {
                    if(this.chart.renderTo.id == "DwellBreakdown_chart") {
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

/**
 * Fill in graph options and call graph api
 * 
 * @param array selected period data
 * @param array previous comparison data   
 */
function drawGraphs(graph_data, comparison_graph_data) {
    $('.graph').fadeIn();
    $('.metric').each(function() {
        var metric = $(this).data('metric');
        var color = $(this).data('color');

        var suffix = '';
        var prefix = '';
        if($(this).data('tooltip-suffix')){
            suffix = $(this).data('tooltip-suffix');
        }

        if($(this).data('tooltip-prefix')){
            prefix = $(this).data('tooltip-prefix');
        }

        var total_points = graph_data.xIntervals.length;

        if(total_points>10){
            graphTickInterval = Math.floor(total_points/4);
        } else {
            graphTickInterval = 1;
        }
    
        var metric_chart = {
            chartContent: metric + '_chart',
            options: {
                colors:[
                    '#'+color,
                    '#4e5c60'
                ],
                xAxis : [{
                    categories : graph_data.xIntervals,
                    tickPosition: 'outside',
                    tickmarkPlacement : 'on',
                    tickInterval : graphTickInterval
                }],
                series:[{
                    name: (global_date_start == global_date_end) ? global_date_start : (global_date_start + ' - ' + global_date_end),
                    data : graph_data[metric],
                    zIndex: 10,
                    tooltip: {
                        valueSuffix: suffix,
                        valuePrefix: prefix
                    }
                },
                {
                    name: (global_date_start2 == global_date_end2 ? global_date_start2 : (global_date_start2+' - '+global_date_end2)),
                    type : 'spline',
                    data : comparison_data[metric],
                    zIndex: 0,
                    tooltip: {
                        valueSuffix: suffix,
                        valuePrefix: prefix
                    }
                }]
            }
        };

        metric_chart = jQuery.extend(true, {}, chart_defaults, metric_chart);
        metric_chart.init(metric_chart.options);
        metric_chart.create();
    });
}

var csvValue = function(id) {
    var prevLen = null;
    if (!$('#' + id + ' tbody tr td').hasClass('tableEmpty')) {
        var avTable = $('#' + id).dataTable();
        var avSettings = avTable.fnSettings();
        if (avSettings) {
            prevLen = avSettings._iDisplayLength;
            avSettings._iDisplayLength = -1;
            avTable.fnDraw();
        }
    }

    var csvText = $("#" + id).table2CSV({delivery: 'value', fullExport:true});

    if (prevLen != null) {
        avSettings._iDisplayLength = prevLen;
        avTable.fnDraw();
    }

    return csvText;
}

function getCSVData(id) {
    event.preventDefault();

    $("#csv_text").val(csvValue(id));

    if (id == "nw_email_list")   $("#csv_filename").val("emails_captured");
        
    if (id == "nw_website_list") $("#csv_filename").val("sites_visited"); 

    $('#csvExport').submit();
    if(!isAdmin){
        mixpanel.track("Network Analytics CSV Downloaded");
    }

};

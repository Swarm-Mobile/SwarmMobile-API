jQuery.fn.keyMetric = function(options) {
    var options = jQuery.extend({
        data        : {},
        title       : 'Key Metric',
        cValue      : 0,
        type        : 'num',
        lValue      : false,
        icon        : false,
        color       : false,
        comparison  : false
    },
    options);
    title = ($(this).attr('swarm-title') !== '')?$(this).attr('swarm-title'):options.title;
    color = ($(this).attr('swarm-color') !== '')?$(this).attr('swarm-color'):options.color;
    icon  = ($(this).attr('swarm-icon') !== '')?$(this).attr('swarm-icon'):options.icon;
    //Empty Object
    if(Object.keys(options.data).length === 0){
        var html  = '<h5 class="primaryRegular caps text-center">'+title+'</h5>';
            html += '<div class="focusNumber text-center primaryBold">';
            html += '<i class="circleIcon hidden-xs color_bg'+color;
            html += ' '+icon+'"></i>';
            html += '<span class="varA"><img src="/b2b/images/ajax-loader.gif"></span>';
            html += '<span class="varA"></span>';
            html += '</div>';
            html += '<div class="progress">';
            html += '<div class="progress-bar color_bg'+color+'" role="progressbar"></div>'
            html += '</div>';
            html += '<div class="bottomMetrics">';
            html += '<div class="col-md-6 col-xs-6 subtle small varB"></div>';
            html += '<div class="col-md-6 col-xs-6 subtle small text-right change"></div>';
            html += '</div>';
    } else {
        html = '';
    }
    $(this).html(html);
    
};

jQuery.fn.insightMetric = function(options) {
    var options = jQuery.extend({
        data:{},
        title: 'Insight Metric',
        cValue: 0,
        type: 'num',
        lValue: false,
        color: 1
    },
    options);
    console.log('insight');
};

jQuery.fn.graphMetric = function(options) {
    var options = jQuery.extend({
        data:{},
        title: 'Graph Metric',
        aValues: array()
    },
    options);
    console.log('graph');
};

var dashboard = {
    data_types: [
        'walkbys',
        'footTraffic',
        'transactions',
        'revenue',
        'avgTicket',
        'windowConversion',
        'conversionRate',
        'totals'
    ],
    start_date: '2014-03-01',
    end_date: '2014-03-10',
    member_id: 0,
    access_token: '',
    init: function(member_id, start_date, end_date, access_token) {
        dashboard.member_id     = member_id;
        dashboard.start_date    = start_date;
        dashboard.end_date      = end_date;        
        dashboard.access_token  = access_token;                        
        dashboard.data_types.forEach(function(v) {            
            $('div[swarm-data="' + v + '"]').each(function() {                                
                var container = $(this);
                var display = container.attr('swarm-display');
                eval('container.'+display+"Metric()");
                $.ajax({
                    url: '/api/store/' + $(this).attr('swarm-data'),
                    type: 'GET',
                    data: {
                        member_id: dashboard.member_id,
                        start_date: dashboard.start_date,
                        end_date: dashboard.end_date,
                        access_token: dashboard.access_token
                    },
                    success: function(data) {           
                        eval('container.'+display+"Metric({data:"+data+"})");
                    }
                });
            });
        });
    }
};
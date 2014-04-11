jQuery.fn.monoGraphMetric = function(options) {
    var options = jQuery.extend({
        cData: {},
        pData: {},
        title: 'Key Metric',
        type: 'num',
        icon: false,
        comparison: false,
        info:'open'
    }, options);    
    function init(container) {
        options.title = tools.coalesce(container.attr('swarm-title'), options.title);
        options.type = tools.coalesce(tools.endpointType($(this).attr('swarm-data')), options.type);
        
        dashboard.charts[container.attr('id')] = new Highcharts.Chart({
            chart: {
                renderTo: container.attr('id'),
                type: 'areaspline',
                backgroundColor: '#fff',
                borderColor: '#e9eff0',
                spacingLeft: 30
            },
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
                        s += '<br/><span style="color:'
                                + point.series.color
                                + ';font-weight:bold;padding: 3px 0; display:block;">'
                                + point.series.tooltipOptions.valuePrefix
                                + point.y
                                + point.series.tooltipOptions.valueSuffix
                                + '</span>';
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
                        lineColor: null
                    }
                }
            },
            title: {text: null},
            xAxis: [{
                    categories: [],
                    tickPosition: 'outside',
                    tickmarkPlacement: 'on',
                    tickInterval: 1
                }]
        });
        dashboard.charts[container.attr('id')].addAxis({
            min: 0,
            allowDecimals: false,
            title: {
                text: null,
                style: {
                    color: '#1b2224',
                    fontWeight: 'bold'
                }
            }
        });
    }
    function render(container, options, source) {
        var data = [0];
        var categories = ['Open'];
        var series_name = '';        
        if (Object.keys(options[source.name].data.breakdown).length === 1) {
            for (var i = 0; i < 24; i++) {        
                var k = (i < 10) ? '0' + i : '' + i;
                var v = options[source.name].data.breakdown[options[source.name].options.start_date].hours[k];                
                if (options.info === 'open' && v.open) {                    
                    data.push(v.total);
                    categories.push(((k > 11) ? ((k === 12) ? 12 : ((k % 13) + 1)) + ' PM' : k + ' AM'));
                } else if (options.info === 'close' && !v.open) {
                    data.push(v.total);
                    categories.push(((k > 11) ? ((k === 12) ? 12 : ((k % 13) + 1)) + ' PM' : k + ' AM'));
                } else if (options.info === 'total') {
                    data.push(v.total);
                    categories.push(((k > 11) ? ((k === 12) ? 12 : ((k % 13) + 1)) + ' PM' : k + ' AM'));
                }
            }
            series_name = tools.parseDate(options[source.name].options.start_date);
        } else {
            $.each(options[source.name].data.breakdown, function(k, v) {
                data.push(v['totals'][options.info]);
                categories.push(k);
            });
            series_name = tools.parseDate(options[source.name].options.start_date);
            series_name += ' - ', tools.parseDate(options[source.name].options.end_date);
        }
        categories.push('Close');
        data.push(0);        
        dashboard.charts[container.attr('id')].xAxis[0].categories = categories;
        dashboard.charts[container.attr('id')].xAxis[0].options.tickInterval = ((categories.length > 10) ? Math.floor(categories.length / 4) : 1);
        dashboard.charts[container.attr('id')].yAxis[0].update({title: {text: null}});
        dashboard.charts[container.attr('id')].addSeries({
            name: series_name,
            min: 0,
            allowDecimals: false,
            lineWidth: 2,
            type: source.type,
            color: source.color,
            yAxis: 0,
            zIndex: 0,
            data: data,
            tooltip: {
                valueSuffix: (options.type === 'currency') ? '$' : '',
                valuePrefix: (options.type === 'rate') ? '%' : ''
            }
        });
    }

    var container = $(this);
    if (Object.keys(options.cData).length === 0) {
        init($(this));
    } else {
        title = tools.coalesce(container.attr('swarm-title'), options.title);
        var sources = [
            {
                name: 'cData',
                type: 'areaspline',
                color: tools.endpointColor(container.attr('swarm-data'))
            },
            {
                name: 'pData',
                type: 'spline',
                color: '#4e5c60'
            }
        ];        
        sources.forEach(function(source) {
            render(container, options, source);
        });
    }
};
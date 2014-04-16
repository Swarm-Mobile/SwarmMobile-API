jQuery.fn.multiGraphMetric = function(options) {
    var options = jQuery.extend({
        cData: {},
        info: 'open',
        title: 'Graph Metric',
        axis_title: 'Axis Title',
        type: 'num'
    }, options);

    function init(container) {
        options.title = tools.coalesce(container.attr('swarm-title'), options.title);
        options.type = tools.coalesce(tools.endpointType($(this).attr('swarm-data')), options.type);
        options.axis_title = tools.coalesce(container.attr('swarm-axis-title'), options.axis_title);
        options.info = tools.coalesce($(this).attr('swarm-info'), options.info);
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
                    tickInterval: 1,
                    title: {
                        text: options.title,
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
                        text: options.axis_title,
                        style: {
                            color: '#1b2224',
                            fontWeight: 'bold'
                        }
                    }
                },
                {
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
                        text: '',
                        style: {
                            color: '#1b2224',
                            fontWeight: 'bold'
                        }
                    }
                }]
        });
    }
    function render(container, options, source) {
        var data = [];
        var categories = [];
        var series_name = '';
        series_name = options.cData.options.endpoint;
        series_name = series_name.substring(series_name.indexOf('/', 1) + 1);
        series_name = tools.ucwords(series_name);
        if (source.type === 'spline') {
            dashboard.charts[container.attr('id')].yAxis[1].update({title: {text: series_name}});
        }
        if (Object.keys(options.cData.data.breakdown).length === 1) {
            categories.push('Open');
            data.push(0);
            var ini = 0;
            var end = 23;            
            if (nightclub) {                
                for (var i = 23; i >= 0; i--) {
                    var k = (i%24 < 10) ? '0' + i%24 : '' + i%24;
                    var v = options.cData.data.breakdown[options.cData.options.start_date].hours[k];                    
                    if(!v.open){
                        ini = i+1;
                        end = (i===0)?23:(ini-1);
                        break;
                    }
                }
            }
            for (var i = ini; (i%24) + ini !== end + ini; i++) {                
                var k = (i%24 < 10) ? '0' + i%24 : '' + i%24;                
                var v = options.cData.data.breakdown[options.cData.options.start_date].hours[k];                
                k = parseInt(k);
                var category = (k === 0)?'12 AM':((k%12)+((k>12)?' PM':' AM'));
                if (options.info === 'open' && v.open) {
                    data.push(v.total);
                    categories.push(category);
                } else if (options.info === 'close' && !v.open) {
                    data.push(v.total);
                    categories.push(category);
                } else if (options.info === 'total') {
                    data.push(v.total);
                    categories.push(category);
                }
            }
            categories.push('Close');     
            data.push(0);
        } else {
            $.each(options.cData.data.breakdown, function(k, v) {
                data.push(v['totals'][options.info]);
                categories.push(k);
            });
        }
        dashboard.charts[container.attr('id')].xAxis[0].categories = categories;
        var yAxis = (source.type === 'spline') ? 1 : 0;
        if (source.type === 'spline') {
            dashboard.charts[container.attr('id')].addSeries({
                name: series_name,
                min: 0,
                allowDecimals: false,
                lineWidth: 2,
                type: source.type,
                color: source.color,
                yAxis: yAxis,
                visible: (yAxis === 0),
                zIndex: 0,
                data: data,
                marker: {
                    symbol: 'circle',
                    fillColor: '#4e5c60'
                },
                tooltip: {
                    valuePrefix: currency,
                    valueSuffix: ''
                }
            });
        } else {
            dashboard.charts[container.attr('id')].addSeries({
                name: series_name,
                min: 0,
                allowDecimals: false,
                lineWidth: 2,
                type: source.type,
                color: source.color,
                yAxis: yAxis,
                visible: (yAxis === 0),
                zIndex: 0,
                data: data,
                tooltip: {
                    valueSuffix: (options.type === 'currency') ? currency : '',
                    valuePrefix: (options.type === 'rate') ? '%' : ''
                }
            });
        }
    }

    var container = $(this);
    if (Object.keys(options.cData).length === 0) {
        init($(this));
    } else {
        title = tools.coalesce(container.attr('swarm-title'), options.title);
        var sources = [];
        data_sources = container.attr('swarm-data').split(',');
        var component = tools.coalesce(container.attr('swarm-component'), 'store');
        for (var i = 0; i < data_sources.length; i++) {
            if ('/'+component+'/' + data_sources[i] === options.cData.options.endpoint) {
                var type = tools.endpointLineType(data_sources[i]);
                sources.push({
                    name: data_sources[i],
                    type: type,
                    color: (type === 'areaspline') ? tools.endpointColor(data_sources[i]) : tools.hex('grey')
                });
            }
        }
        sources.forEach(function(source) {
            render(container, options, source);
        });
    }
};
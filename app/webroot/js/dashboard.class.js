jQuery.fn.keyMetric = function(options) {
    var options = jQuery.extend({
        cData: {},
        pData: {},
        title: 'Key Metric',
        type: 'num',
        icon: false,
        color: 1,
        comparison: false
    }, options);

    comparison = Boolean(tools.coalesce($(this).attr('swarm-comparison'), options.comparison));
    icon = tools.coalesce($(this).attr('swarm-icon'), options.icon);
    color = tools.coalesce(tools.color($(this).attr('swarm-color')), options.color);
    title = tools.coalesce($(this).attr('swarm-title'), options.title);
    type = tools.coalesce($(this).attr('swarm-type'), options.type);
    c = (type === 'currency') ? '$' : '';
    p = (type === 'rate') ? '%' : '';

    var html = '<h5 class="primaryRegular caps text-center">' + title + '</h5>';
    html += '<div class="focusNumber text-center primaryBold">';
    if (icon) {
        html += '<i class="circleIcon hidden-xs color_bg' + color;
        html += ' ' + icon + '"></i>';
    }

    if (Object.keys(options.cData).length === 0) {
        var varA = (Object.keys(options.cData).length === 0) ? 0 : options.cData['totals']['total'];
        html += '<span class="varA" data-metric="0"><img src="/b2b/images/ajax-loader.gif"></span>';
    } else {
        var varA = options.cData['totals']['total'];
        html += '<span class="varA" data-metric="' + varA + '">' + c + varA + p + '</span>';
    }
    html += '</div>';

    if (comparison) {
        var varB = (Object.keys(options.cData).length === 0) ? 0 : options.pData['totals']['total'];
        var percentage = (varB === 0) ? 0 : Math.round(varA / varB);
        var sign = ((varA > varB) ? '+' : '-');
        var color_class = ((varB > varA) ? 'text-danger' : 'text-success');

        html += '<div class="progress">';
        html += '<div class="progress-bar color_bg' + color + '" ';
        html += 'role="progressbar" style="width: ' + percentage + '%;"></div>';
        html += '</div>';
        html += '<div class="bottomMetrics">';
        html += '<div class="col-md-6 col-xs-6 subtle small varB" ';
        html += 'data-metric="' + varB + '">vs. ' + c + varB + p + '</div>';
        html += '<div class="col-md-6 col-xs-6 subtle small text-right change">';
        html += '<p class="changeOverview ' + color_class + '"><span></span>';
        html += sign + percentage + '%</p></div>';
        html += '</div>';
    }
    $(this).html(html);
};
jQuery.fn.insightMetric = function(options) {
    var options = jQuery.extend({
        cData: {},
        pData: {},
        title: 'Insight Metric',
        type: 'num',
        color: 1,
        comparison: false
    }, options);

    comparison = Boolean(tools.coalesce($(this).attr('swarm-comparison'), options.comparison));
    icon = tools.coalesce($(this).attr('swarm-icon'), options.icon);
    color = tools.coalesce(tools.color($(this).attr('swarm-color')), options.color);
    title = tools.coalesce($(this).attr('swarm-title'), options.title);
    type = tools.coalesce($(this).attr('swarm-type'), options.type);
    c = (type === 'currency') ? '$' : '';
    p = (type === 'rate') ? '%' : '';

    var varA = (Object.keys(options.cData).length === 0) ? 0 : options.cData['totals']['total'];
    var html = '<div class="insight color_border' + color + '" data-title="' + title + '">';
    html += '<div class="primaryRegular caps small">' + title + '</div>';
    html += '<div class="small subtle pull-right text-right change">';
    if (comparison) {
        var varB = (Object.keys(options.cData).length === 0) ? 0 : options.pData['totals']['total'];
        var percentage = (varB === 0) ? 0 : Math.round(varA / varB);
        var sign = ((varA > varB) ? '+' : '-');
        var color_class = ((varB > varA) ? 'text-danger' : 'text-success');
        html += '<p class="changeOverview ' + color_class + '"><span></span>' + sign + percentage + '%</p></div>';
        html += '<div class="varB hide" data-metric="' + varB + '" style="display: block;">vs. ' + c + varB + p + '</div>';
    }
    if (Object.keys(options.cData).length === 0) {
        html += '<div class="secondaryNumber primaryBold varA" data-metric="0"><img src="/b2b/images/ajax-loader.gif"></div>';
    } else {
        html += '<div class="secondaryNumber primaryBold varA" data-metric="' + varA + '">' + c + varA + p + '</div>';
    }
    $(this).html(html);
};
jQuery.fn.monoGraphMetric = function(options) {
    var options = jQuery.extend({
        cData: {},
        pData: {},
        info: 'open',
        title: 'Graph Metric'
    }, options);

    function init(container) {
        dashboard.charts[container.attr('id')] = new Highcharts.Chart({
            chart: {
                renderTo: container.attr('id'),
                type: 'areaspline',
                backgroundColor: '#fff',
                borderColor: '#e9eff0'
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
                                + point.series.name + ': '
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
                }]
        });
    }
    function render(container, options, source) {
        dashboard.charts[container.attr('id')].addAxis({
            opposite: source.opposite,
            min: 0,
            allowDecimals: false,
            gridLineColor: '#e9eff0',
            lineColor: '#e9eff0',
            lineWidth: 1,
            title: {
                text: title,
                style: {
                    color: '#1b2224',
                    fontWeight: 'bold'
                }
            }
        });
        var data = [];
        var categories = [];
        if (Object.keys(options[source.name].breakdown).length === 1) {
            $.each(options[source.name].breakdown[options[source.name].options.start_date].hours, function(k, v) {
                if (options.info == 'open' && v.open) {
                    data.push(v.total);
                    categories.push(k);
                } else if (options.info == 'close' && !v.open) {
                    data.push(v.total);
                    categories.push(k);
                } else if (options.info == 'total') {
                    data.push(v.total);
                    categories.push(k);
                }
            });
        } else {
            $.each(options[source.name].breakdown, function(k, v) {
                data.push(v['totals'][options.info]);
                categories.push(k);
            });
        }
        dashboard.charts[container.attr('id')].xAxis[0].categories = categories;
        dashboard.charts[container.attr('id')].addSeries({
            name: options[source.name].options.start_date,
            type: source.type,
            color: source.color,
            visible: true,
            yAxis: 0,
            lineWidth: 2,
            tooltip: {
                valuePrefix: '',
                valueSuffix: ''
            },
            data: data
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
                opposite: false,
                type: 'areaspline',
                color: tools.endpointColor(container.attr('swarm-data'))
            },
            {
                name: 'pData',
                opposite: true,
                type: 'spline',
                color: '#4e5c60'
            }
        ];
        sources.forEach(function(source) {
            render(container, options, source);
        });
    }
};
jQuery.fn.graphMetric = function(options) {
    var options = jQuery.extend({
        cData: {},
        pData: {},
        info: 'open',
        title: 'Graph Metric',
        comparison: false
    }, options);

    var container = $(this);
    if (Object.keys(options.cData).length === 0) {
        dashboard.init_chart($(this));
    } else {
        comparison = Boolean(tools.coalesce(container.attr('swarm-comparison'), options.comparison));
        title = tools.coalesce(container.attr('swarm-title'), options.title);
        if (comparison) {
            var sources = [
                {name: 'cData', opposite: false, type: 'areaspline', color: tools.endpointColor(container.attr('swarm-data'))},
                {name: 'pData', opposite: true, type: 'spline', color: '#4e5c60'}
            ];
            sources.forEach(function(source) {
                dashboard.charts[container.attr('id')].addAxis({
                    opposite: source.opposite,
                    min: 0,
                    allowDecimals: false,
                    gridLineColor: '#e9eff0',
                    lineColor: '#e9eff0',
                    lineWidth: 1,
                    title: {
                        text: title,
                        style: {
                            color: '#1b2224',
                            fontWeight: 'bold'
                        }
                    }
                });
                var data = [];
                var categories = [];
                if (Object.keys(options[source.name].breakdown).length === 1) {
                    $.each(options[source.name].breakdown[options[source.name].options.start_date].hours, function(k, v) {
                        if (options.info == 'open' && v.open) {
                            data.push(v.total);
                            categories.push(k);
                        } else if (options.info == 'close' && !v.open) {
                            data.push(v.total);
                            categories.push(k);
                        } else if (options.info == 'total') {
                            data.push(v.total);
                            categories.push(k);
                        }
                    });
                } else {
                    $.each(options[source.name].breakdown[options[source.name].options.start_date], function(k, v) {
                        data.push(v['totals'][options.info]);
                        categories.push(k);
                    });
                }
                dashboard.charts[container.attr('id')].xAxis[0].categories = categories;
                dashboard.charts[container.attr('id')].addSeries({
                    name: options[source.name].options.start_date,
                    type: source.type,
                    color: source.color,
                    visible: true,
                    yAxis: 0,
                    lineWidth: 2,
                    tooltip: {
                        valuePrefix: '',
                        valueSuffix: ''
                    },
                    data: data
                });
            });
        } else {

        }
    }
};

var tools = {
    defaults: function(type) {
        if (type === 'start_date' || type === 'end_date') {
            d = new Date();
            d = d.toISOString();
            i = d.indexOf('T');
            return d.substring(0, i);
        }
        if (type === 'previous_start_date' || type === 'previous_end_date') {
            d = new Date();
            d.setDate(d.getDate() - 1);
            d = d.toISOString();
            i = d.indexOf('T');
            return d.substring(0, i);
        }
    },
    coalesce: function(nvalue, cvalue) {
        if (typeof (nvalue) !== 'undefined' && nvalue !== '') {
            return nvalue;
        }
        return cvalue;
    },
    color: function(value) {
        var colors = [
            {id: 1, name: 'blue'},
            {id: 2, name: 'green'},
            {id: 3, name: 'orange'},
            {id: 4, name: 'yellow'},
            {id: 5, name: 'violet'},
            {id: 6, name: 'green2'},
            {id: 7, name: 'green3'},
            {id: 8, name: 'grey'}
        ];
        var result = 1;
        colors.forEach(function(v) {
            if (value === '' + v.id || value === v.id || value === v.name) {
                result = v.id;
                return;
            }
        });
        return result;
    },
    endpointColor: function(endpoint) {
        var colors = [
            {name: 'walkbys', color: '#3498db'},
            {name: 'footTraffic', color: '#27ae60'},
            {name: 'transactions', color: '#fd7037'},
            {name: 'revenue', color: '#f39c12'},
            {name: 'avgTicket', color: '#f39c12'},
            {name: 'windowConversion', color: '#3498db'},
            {name: 'conversionRate', color: '#fd7037'}
        ];
        var result = '#3498db';
        colors.forEach(function(v) {
            if (endpoint === v.name) {
                result = v.color;
                return;
            }
        });
        return result;
    }
}
var dashboard = {
    start_date: tools.defaults('start_date'),
    end_date: tools.defaults('end_date'),
    p_start_date: tools.defaults('previous_start_date'),
    p_end_date: tools.defaults('previous_end_date'),
    member_id: 0,
    access_token: '',
    charts: {},
    init: function(member_id, access_token, start_date, end_date, p_start_date, p_end_date) {
        dashboard.member_id = member_id;
        dashboard.access_token = access_token;
        dashboard.start_date = tools.coalesce(start_date, dashboard.start_date);
        dashboard.end_date = tools.coalesce(start_date, dashboard.end_date);
        dashboard.p_start_date = tools.coalesce(start_date, dashboard.p_start_date);
        dashboard.p_end_date = tools.coalesce(start_date, dashboard.p_end_date);

        dashboard.start_date = '2014-03-01';
        dashboard.end_date = '2014-03-05';
        dashboard.p_start_date = '2014-02-20';
        dashboard.p_end_date = '2014-02-25';

        $('div[swarm-data]').each(function() {
            var container = $(this);
            var display = container.attr('swarm-display');
            var resources = container.attr('swarm-data').split(',');
            container[display + "Metric"]();
            resources.forEach(function(resource) {
                $.ajax({
                    url: '/api/store/' + resource,
                    type: 'GET',
                    data: {
                        member_id: dashboard.member_id,
                        start_date: dashboard.start_date,
                        end_date: dashboard.end_date,
                        access_token: dashboard.access_token
                    },
                    success: function(cData) {
                        if (container.attr('swarm-comparison') === 'true') {
                            $.ajax({
                                url: '/api/store/' + resource,
                                type: 'GET',
                                data: {
                                    member_id: dashboard.member_id,
                                    start_date: dashboard.p_start_date,
                                    end_date: dashboard.p_end_date,
                                    access_token: dashboard.access_token
                                },
                                success: function(pData) {
                                    container[display + "Metric"]({cData: cData, pData: pData});
                                }
                            });
                        } else {
                            container[display + "Metric"]({cData: cData});
                        }
                    }
                });
            });

        });
    }
};
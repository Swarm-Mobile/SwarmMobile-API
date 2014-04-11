jQuery.fn.insightMetric = function(options) {
    var options = jQuery.extend({
        cData: {},
        pData: {},
        title: 'Insight Metric',
        type: 'num',
        comparison: false
    }, options);

    comparison = Boolean(tools.coalesce($(this).attr('swarm-comparison'), options.comparison));
    icon = tools.coalesce($(this).attr('swarm-icon'), options.icon);    
    color = tools.color(tools.hex(tools.endpointColor($(this).attr('swarm-data'))));
    title = tools.coalesce($(this).attr('swarm-title'), options.title);
    type = tools.coalesce($(this).attr('swarm-type'), options.type);
    c = (type === 'currency') ? '$' : '';
    p = (type === 'rate') ? '%' : '';

    var varA = (Object.keys(options.cData).length === 0) ? 0 : options.cData.data['totals']['total'];
    var html = '<div class="insight color_border' + color + '" data-title="' + title + '">';
    html += '<div class="primaryRegular caps small">' + title + '</div>';
    html += '<div class="small subtle pull-right text-right change">';
    if (comparison) {
        var varB = (Object.keys(options.cData).length === 0) ? 0 : options.pData.data['totals']['total'];
        var percentage = (varB === 0) ? 0 : Math.round(varA / varB);
        var sign = ((varA > varB) ? '+' : '-');
        var color_class = ((varB > varA) ? 'text-danger' : 'text-success');
        varB = (type === 'time')?tools.makeHMS(varB):varB;
        html += '<p class="changeOverview ' + color_class + '"><span></span>' + sign + percentage + '%</p></div>';
        html += '<div class="varB hide" data-metric="' + varB + '" style="display: block;">vs. ' + c + varB + p + '</div>';
    }
    varA = (type === 'time')?tools.makeHMS(varA):varA;
    if (Object.keys(options.cData).length === 0) {
        html += '<div class="secondaryNumber primaryBold varA" data-metric="0"><img src="/b2b/images/ajax-loader.gif"></div>';
    } else {
        html += '<div class="secondaryNumber primaryBold varA" data-metric="' + varA + '">' + c + varA + p + '</div>';
    }
    $(this).html(html);
};
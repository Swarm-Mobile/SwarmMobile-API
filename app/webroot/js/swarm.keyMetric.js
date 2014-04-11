jQuery.fn.keyMetric = function(options) {    
    var options = jQuery.extend({
        cData: {},
        pData: {},
        title: 'Key Metric',
        type: 'num',
        icon: true,
        comparison: false
    }, options);

    comparison = Boolean(tools.coalesce($(this).attr('swarm-comparison'), options.comparison));
    icon = tools.coalesce($(this).attr('swarm-icon'), options.icon);
    if(icon){icon = tools.endpointIcon($(this).attr('swarm-data'));}
    color = tools.color(tools.hex(tools.endpointColor($(this).attr('swarm-data'))));
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
        var varA = (Object.keys(options.cData).length === 0) ? 0 : options.cData.data['totals']['total'];
        html += '<span class="varA" data-metric="0"><img src="/b2b/images/ajax-loader.gif"></span>';
    } else {
        var varA = options.cData.data['totals']['total'];
        tmp = (type === 'time')?tools.makeHMS(varA):varA;
        html += '<span class="varA" data-metric="' + tmp + '">' + c + tmp + p + '</span>';
    }
    html += '</div>';

    if (comparison) {
        var varB = (Object.keys(options.cData).length === 0) ? 0 : options.pData.data['totals']['total'];
        var percentage = (varB === 0) ? 0 : Math.round(varA / varB);
        var sign = ((varA > varB) ? '+' : '-');
        var color_class = ((varB > varA) ? 'text-danger' : 'text-success');
        varB = (type === 'time')?tools.makeHMS(varB):varB;
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
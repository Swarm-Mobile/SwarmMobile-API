jQuery.fn.breakdownGraphMetric = function(options) {
    var options = jQuery.extend({
        cData: {},
        pData: {},
        info: 'open',
        title: 'Breakdown Metric',
        type: 'num',
        icon: false,
        comparison: false
    }, options);
    options.type = tools.coalesce(tools.endpointType($(this).attr('swarm-data')), options.type);
    function render(source) {
        var html = '';
        var c = (options.type === 'currency') ? currency : '';
        var p = (options.type === 'rate') ? '%' : '';
        if (Object.keys(source.data.breakdown).length === 1) {
            for (var i = 0; i < 24; i++) {
                var k = (i < 10) ? '0' + i : '' + i;
                var v = source.data.breakdown[source.options.start_date].hours[k];
                var total = (options.type === 'time')?tools.makeHMS(v.total):tools.addCommas(v.total);
                if (options.info === 'open' && v.open) {
                    var hour = ((k > 11) ? ((k === 12) ? 12 : ((k % 13) + 1)) + ' PM' : k + ' AM');
                    html += '<p><span class="primaryBold metricTitle">' + hour + ':</span>';
                    html += '<span class="metricData pull-right">' + c + total + p + '</span></p>';
                } else if (options.info === 'close' && !v.open) {
                    var hour = ((k > 11) ? ((k === 12) ? 12 : ((k % 13) + 1)) + ' PM' : k + ' AM');
                    html += '<p><span class="primaryBold metricTitle">' + hour + ':</span>';
                    html += '<span class="metricData pull-right">' + c + total + p + '</span></p>';
                } else if (options.info === 'total') {
                    var hour = ((k > 11) ? ((k === 12) ? 12 : ((k % 13) + 1)) + ' PM' : k + ' AM');
                    html += '<p><span class="primaryBold metricTitle">' + hour + ':</span>';
                    html += '<span class="metricData pull-right">' + c + total + p + '</span></p>';
                }
            }
        } else {
            $.each(source.data.breakdown, function(k, v) {
                var total = (options.type === 'time')?tools.makeHMS(v['totals'][options.info]):tools.addCommas(v['totals'][options.info]);
                html += '<p><span class="primaryBold metricTitle">' + k + ':</span>';
                html += '<span class="metricData pull-right">' + c + total + p + '</span></p>';
            });
        }
        return html;
        drilldown_html += '<p><span class="primaryBold metricTitle">9 AM:</span>';
        drilldown_html += '<span class="metricData pull-right"> 1.25%</span></p>';
    }

    var container = $(this);
    var title = tools.coalesce(container.attr('swarm-title'), options.title);

    //KEY METRIC
    var key_html = '<div class="col-md-3" style="padding-top:40px"';
    key_html += 'swarm-data="' + container.attr('swarm-data') + '"';
    key_html += ' swarm-display="key"';
    key_html += ' swarm-title="' + title + '"';
    key_html += 'swarm-type="' + options.type + '"';
    key_html += 'swarm-comparison="true">';
    key_html += '</div>';
    var keyMetric = $(key_html);
    keyMetric.keyMetric({cData: options.cData, pData: options.pData});
    var prepend_html = '<a href="#" class="black blackHover glyphicon ';
    prepend_html += 'glyphicon-info-sign drilldownTrigger pull-right"></a>';
    keyMetric.prepend(prepend_html);
    $(this).html(keyMetric);

    //MONOGRAPH METRIC
    var monoGraph_html = '<div class="col-md-9" id="' + container.attr('id') + '_monograph" style="height:250px"';
    monoGraph_html += 'swarm-data="' + container.attr('swarm-data') + '"';
    monoGraph_html += 'swarm-display="monoGraph"';
    monoGraph_html += 'swarm-title=""';
    monoGraph_html += 'swarm-type="' + options - type + '"';
    monoGraph_html += '></div>';
    var monoGraphMetric = $(monoGraph_html);
    $(this).append(monoGraphMetric);    
    monoGraphMetric.monoGraphMetric();    
    monoGraphMetric.monoGraphMetric({cData: options.cData, pData: options.pData});    

    //DRILLDOWN METRIC
    if (Object.keys(options.cData).length !== 0) {
        color = tools.color(tools.hex(tools.endpointColor($(this).attr('swarm-data'))));
        var drilldown_html = '<div class="drilldown col-md-9 col-md-offset-3">';
        drilldown_html += '<div class="col-md-6 col-xs-6 global drilldownTarget color_border' + color + '">';
        drilldown_html += render(options.cData);
        drilldown_html += '</div>';
        drilldown_html += '<div class="col-md-6 col-xs-6 comparison drilldownTarget">';
        drilldown_html += render(options.pData);
        drilldown_html += '</div>';
        drilldown_html += '</div>';
        var drilldown = $(drilldown_html);
        $(this).append(drilldown);
    }
};
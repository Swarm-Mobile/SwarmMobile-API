var dashboard = {
    start_date: tools.defaults('start_date'),
    end_date: tools.defaults('end_date'),
    p_start_date: tools.defaults('previous_start_date'),
    p_end_date: tools.defaults('previous_end_date'),
    member_id: 0,
    access_token: '',
    charts: {},
    render: function(start_date, end_date, p_start_date, p_end_date) {
        dashboard.start_date = tools.coalesce(start_date, dashboard.start_date);
        dashboard.end_date = tools.coalesce(end_date, dashboard.end_date);
        dashboard.p_start_date = tools.coalesce(p_start_date, dashboard.p_start_date);
        dashboard.p_end_date = tools.coalesce(p_end_date, dashboard.p_end_date);
        dashboard.member_id = tools.coalesce(member_id, dashboard.p_end_date);
        $('div[swarm-data]').each(function() {
            var container = $(this);
            var display = container.attr('swarm-display');
            var resources = container.attr('swarm-data').split(',');
            var component = tools.coalesce(container.attr('swarm-component'), 'store');            
            container[display + "Metric"]();
            resources.forEach(function(resource) {
                $.ajax({
                    url: api_url+component+'/' + resource,
                    type: 'GET',
                    data: {
                        member_id: dashboard.member_id,
                        start_date: dashboard.start_date,
                        end_date: dashboard.end_date,
                        access_token: dashboard.access_token,
                        nocache: no_cache,
                        norollups: no_rollups
                    },
                    success: function(cData) {
                        if (container.attr('swarm-comparison') === 'true') {
                            $.ajax({
                                url: api_url+component+'/'+resource,
                                type: 'GET',
                                data: {
                                    member_id: dashboard.member_id,
                                    start_date: dashboard.p_start_date,
                                    end_date: dashboard.p_end_date,
                                    access_token: dashboard.access_token,
                                    nocache: no_cache,
                                    norollups: no_rollups
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
    },
    init: function(member_id, access_token, start_date, end_date, p_start_date, p_end_date) {
        dashboard.member_id = member_id;
        dashboard.access_token = access_token;
        dashboard.start_date = tools.coalesce(start_date, dashboard.start_date);
        dashboard.end_date = tools.coalesce(end_date, dashboard.end_date);
        dashboard.p_start_date = tools.coalesce(p_start_date, dashboard.p_start_date);
        dashboard.p_end_date = tools.coalesce(p_end_date, dashboard.p_end_date);
        dashboard.render();
    }
};
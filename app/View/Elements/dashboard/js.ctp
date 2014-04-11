<script type="text/javascript">
    var member_id = '{member_id}';
            var isAdmin = false;
            var token = 'jS1HO92WZYF6erM73WaqVM0O9NhdD0MN';
            // FOR DEBUG: var isAdmin = false;
</script>
<script src="/js/bootstrap.3.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.min.js"></script>
<script src="/js/cpanel.js?v=1.1"></script>
<script type="text/javascript">
    var username = '{username}';
    var ap_id = '0';
    var aid = "{ga_profile_id}";
    var currency = "$";
    var lightspeed_id = '0';
    var no_rollups = '1';
    var no_cache = '1';
    var demo = 'no';

    $(function () {
        $('.metric, .insight').on('click', function(){
            var title = $(this).data('title');
            window.location = '/store_analytics#' + title;
        });
    });
</script>
<script src="/js/highcharts.3.0.9.js"></script>
<script type="text/javascript" src="/js/moment.min.js"></script>
<script type="text/javascript" src="/js/dashboard-datepicker.js"></script>
<script type="text/javascript" src="/js/daterangepicker.js"></script>
<!--script type="text/javascript" src="/js/dashboard.live.js?v=3.2"></script-->
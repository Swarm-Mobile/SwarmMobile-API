<script type='text/javascript' src='/js/swarm.keyMetric.js'></script>
<script type='text/javascript' src='/js/swarm.insightMetric.js'></script>
<script type='text/javascript' src='/js/swarm.monoGraphMetric.js'></script>
<script type='text/javascript' src='/js/swarm.multiGraphMetric.js'></script>
<script type='text/javascript' src='/js/swarm.breakdownGraphMetric.js'></script>
<script type='text/javascript' src='/js/swarm.tools.class.js'></script>
<script type='text/javascript' src='/js/swarm.dashboard.class.js'></script>
<script type='text/javascript'>
    $(document).ready(function() {
        //member_id = 2;
        //member_id = 698;
        member_id = 689;
        access_token = '<?= $access_token ?>';
        nightclub = 1;
        norollups = 0;
        nocache = 1;
        //dashboard.init(member_id, access_token, '2013-12-02', '2013-12-02', '2013-11-25', '2013-11-25');
        dashboard.init(member_id, access_token, '2014-01-01', '2014-01-15', '2013-12-16', '2013-12-31');
        currency = '$';
    });
</script>
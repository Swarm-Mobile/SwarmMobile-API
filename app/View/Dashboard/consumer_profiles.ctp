<?php $this->assign('title', 'Consumer Profiles'); ?>
<script type='text/javascript' src='/js/swarm.keyMetric.js'></script>
<script type='text/javascript' src='/js/swarm.insightMetric.js'></script>
<script type='text/javascript' src='/js/swarm.monoGraphMetric.js'></script>
<script type='text/javascript' src='/js/swarm.multiGraphMetric.js'></script>
<script type='text/javascript' src='/js/swarm.breakdownGraphMetric.js'></script>
<script type='text/javascript' src='/js/swarm.tools.class.js'></script>
<script type='text/javascript' src='/js/swarm.dashboard.class.js'></script>
<script type='text/javascript'>
    $(document).ready(function() {
        member_id = 689;
        access_token = '<?= $access_token ?>';
        currency = '$';
        dashboard.init(member_id, access_token);
    });
</script>
<div class="row">
    <div class="col-md-12 dataRow">
        <?= $this->element('dashboard/export-button') ?>  
        <?= $this->element('dashboard/show-hide-metrics') ?>
        <?= $this->element('dashboard/refresh-button') ?>        
        <h2 class="headerSpacing">Key Metrics</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-3"
         swarm-data="revenue"
         swarm-display="key"         
         swarm-title="Revenue"
         swarm-icon="false"              
         ></div>
    <div class="col-md-3"
         swarm-data="avgTicket"
         swarm-display="key"         
         swarm-title="Avg. Ticket"
         swarm-icon="false"              
         ></div>
    <div class="col-md-3"
         swarm-data="transactions"
         swarm-display="key"         
         swarm-title="Transactions"
         swarm-icon="false"              
         ></div>
    <div class="col-md-3"
         swarm-data="itemsPerTransaction"
         swarm-display="key"         
         swarm-title="Items / Transaction"
         swarm-icon="false"              
         ></div>
</div>
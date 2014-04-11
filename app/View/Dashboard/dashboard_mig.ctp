<?php $this->assign('title', 'Dashboard'); ?>
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
         swarm-data="walkbys"
         swarm-display="key"         
         swarm-title="Walkbys"
         swarm-comparison="true"         
         ></div>
    <div class="col-md-3"
         swarm-data="footTraffic"
         swarm-display="key"          
         swarm-title="Visitors"
         swarm-comparison="true"         
         ></div>
    <div class="col-md-3"
         swarm-data="returning"
         swarm-display="key"          
         swarm-title="Returning Visitors"
         swarm-comparison="true"         
         ></div>
    <div class="col-md-3"
         swarm-data="dwell"
         swarm-display="key"          
         swarm-title="Avg Dwell Time"
         swarm-comparison="true"         
         ></div>
</div>
<!--
<div class="row"
     id="breakdownGraph"
     swarm-data="walkbys"
     swarm-display="breakdownGraph"     
     swarm-comparison="true"     
     swarm-type="num"
>
</div>
-->
<!--
<div class="row">
    <div clasS="col-md-12" id="mono-graph"
         swarm-data="dwell"
         swarm-display="monoGraph"                           
         swarm-title="Dwell Time"
         swarm-type="time"
         ></div>
</div>
-->
<!--
<div class="row">
    <div clasS="col-md-12" id="multi-graph"
         swarm-data="walkbys,transactions,footTraffic,revenue"
         swarm-display="multiGraph"                  
         swarm-axis-title="Shoppers"        
         swarm-title="Store Hours"
         ></div>
</div>
<div class="row">
    <div class="col-md-3"
         swarm-data="walkbys"
         swarm-display="key"         
         swarm-title="Walkbys"
         swarm-comparison="true"         
         ></div>
    <div class="col-md-3"
         swarm-data="footTraffic"
         swarm-display="key"          
         swarm-title="Total Shoppers"
         swarm-comparison="true"         
         ></div>
    <div class="col-md-3"
         swarm-data="transactions"
         swarm-display="key"         
         swarm-title="Transactions"
         swarm-comparison="true"         
         ></div>
    <div class="col-md-3"
         swarm-data="revenue"
         swarm-display="key"         
         swarm-title="Revenue"
         swarm-comparison="true"
         swarm-type="currency"
         ></div>
</div>
<div class="row">
    <div class="col-md-3"
         swarm-data="windowConversion"
         swarm-display="insight"         
         swarm-title="Window Conversion"         
         swarm-comparison="true"
         swarm-type="rate"
         ></div>
        <div class="col-md-3"
         swarm-data=""
         swarm-display="insight"         
         swarm-title="Return Shoppers"         
         swarm-comparison="true"         
         ></div>
        <div class="col-md-3"
         swarm-data="conversionRate"
         swarm-display="insight"         
         swarm-title="Window Conversion"         
         swarm-comparison="true"
         swarm-type="rate"
         ></div>
        <div class="col-md-3"
         swarm-data="avgTicket"
         swarm-display="insight"         
         swarm-title="Average Ticket"         
         swarm-comparison="true"
         swarm-type="currency"
         ></div>
</div>
-->
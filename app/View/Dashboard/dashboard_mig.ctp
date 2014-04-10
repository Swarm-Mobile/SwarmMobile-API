<?php

$this->assign('title','Dashboard'); ?>
<script type='text/javascript' src='/js/dashboard.class.js'></script>
<script type='text/javascript'>
    $(document).ready(function() {
        member_id = 689;
        access_token = '<?= $access_token ?>';
        dashboard.init(member_id, access_token);
    });
</script>
<div class="row">
    <div class="col-md-12 dataRow">
        <?= $this->element('dashboard/export-button')?>  
        <?= $this->element('dashboard/show-hide-metrics')?>
        <?= $this->element('dashboard/refresh-button')?>        
        <h2 class="headerSpacing">Key Metrics</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-3"
         swarm-data="walkbys"
         swarm-display="key"
         swarm-color="blue" 
         swarm-title="Walkbys"
         swarm-icon="footstepsIcon"
         swarm-comparison="true"         
         ></div>
    <div class="col-md-3"
         swarm-data="footTraffic"
         swarm-display="key"
         swarm-color="green" 
         swarm-title="Total Shoppers"
         swarm-icon="guestsIcon"
         swarm-comparison="true"         
         ></div>
    <div class="col-md-3"
         swarm-data="transactions"
         swarm-display="key"
         swarm-color="orange" 
         swarm-title="Transactions"
         swarm-icon="tagIcon"
         swarm-comparison="true"         
         ></div>
    <div class="col-md-3"
         swarm-data="revenue"
         swarm-display="key"
         swarm-color="yellow" 
         swarm-title="Revenue"
         swarm-icon="revenueIcon"
         swarm-comparison="true"
         swarm-type="currency"
         ></div>
</div>
<div class="row">
    <div class="col-md-3"
         swarm-data="windowConversion"
         swarm-display="insight"
         swarm-color="blue" 
         swarm-title="Window Conversion"         
         swarm-comparison="true"
         swarm-type="rate"
         ></div>
        <div class="col-md-3"
         swarm-data=""
         swarm-display="insight"
         swarm-color="green" 
         swarm-title="Return Shoppers"         
         swarm-comparison="true"         
         ></div>
        <div class="col-md-3"
         swarm-data="conversionRate"
         swarm-display="insight"
         swarm-color="orange" 
         swarm-title="Window Conversion"         
         swarm-comparison="true"
         swarm-type="rate"
         ></div>
        <div class="col-md-3"
         swarm-data="avgTicket"
         swarm-display="insight"
         swarm-color="yellow" 
         swarm-title="Average Ticket"         
         swarm-comparison="true"
         swarm-type="currency"
         ></div>
</div>

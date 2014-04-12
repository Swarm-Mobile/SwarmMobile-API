<?php $this->assign('title', 'Consumer Profiles'); ?>
<?= $this->element('dashboard/test') ?>  
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
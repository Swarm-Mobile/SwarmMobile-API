<?php $this->assign('title', 'Network Analytics'); ?>
<?= $this->element('dashboard/test') ?>  
<div class="row">
    <div class="col-md-12 dataRow">
        <?= $this->element('dashboard/export-button') ?>  
        <?= $this->element('dashboard/show-hide-metrics') ?>
        <?= $this->element('dashboard/refresh-button') ?>        
    </div>
</div>
<?php
$aGraphs = array(
    array('data' => 'wifiConnections', 'title' => 'Wi-Fi Connections'),
    array('data' => 'emailsCaptured', 'title' => 'Emails Captured'),    
);
foreach ($aGraphs as $graph) {
    ?>
    <div class="row" id="<?=$graph['data']?>-breakdown"
         swarm-data="<?= $graph['data']?>"
         swarm-display="breakdownGraph"
         swarm-title="<?= $graph['title']?>"
         swarm-comparison="true"
         swarm-component="network"
         swarm-breakdown="false"
         ></div>
<?php } ?>

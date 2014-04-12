<?php $this->assign('title', 'Store Analytics'); ?>
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
    array('data' => 'walkbys', 'title' => 'Walkbys'),
    array('data' => 'windowConversion', 'title' => 'Window Conversion'),
    array('data' => 'footTraffic', 'title' => 'Total Shoppers'),
    array('data' => 'returning', 'title' => 'Return Shoppers'),
    array('data' => 'dwell', 'title' => 'Average Dwell Time'),
    array('data' => 'transactions', 'title' => 'Transactions'),
    array('data' => 'conversionRate', 'title' => 'Conversion Rate'),
    array('data' => 'revenue', 'title' => 'Revenue'),
    array('data' => 'avgTicket', 'title' => 'Average Ticket'),
    array('data' => 'itemsPerTransaction', 'title' => 'Items / Transaction'),
);
foreach ($aGraphs as $graph) {
    ?>
    <div class="row" id="<?=$graph['data']?>-breakdown"
         swarm-data="<?= $graph['data']?>"
         swarm-display="breakdownGraph"
         swarm-title="<?= $graph['title']?>"
         swarm-comparison="true"
         ></div>
<?php } ?>

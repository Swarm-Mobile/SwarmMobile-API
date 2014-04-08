<?php

$this->assign('title','Dashboard'); ?>
<script type='text/javascript' src='/js/dashboard.class.js'></script>
<script type='text/javascript'>
    $(document).ready(function() {
        start_date = '2014-02-01';
        end_date = '2014-03-10';
        member_id = 689;
        access_token = '<?= $access_token ?>';
        dashboard.init(member_id, start_date, end_date, access_token);
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
    <div class="col-md-3 dataRow metric"
         swarm-data="walkbys"
         swarm-display="key"
         swarm-color="1" 
         swarm-title="Walkbys"
         swarm-icon="footstepsIcon"
         swarm-comparison="false"
         swarm-type="num"
         ></div>
</div>

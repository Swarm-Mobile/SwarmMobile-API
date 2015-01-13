<?php if ($reportType !== 'Daily') { ?>
    <h5><?= $reportType ?> Highlights</h5>
    <table class="metric" width="100%">        
        <?= $this->element('metrics_report/highlight', array('metric' => 'highest_footTraffic_hour')) ?>                    
        <?= $this->element('metrics_report/highlight', array('metric' => 'highest_footTraffic_day')) ?>                    
        <?php if (($highest_revenue_hour_value > 0) || $zeroHighlights) { ?>
            <?= $this->element('metrics_report/highlight', array('metric' => 'highest_revenue_hour')) ?>                    
            <?= $this->element('metrics_report/highlight', array('metric' => 'highest_revenue_day')) ?>                    
            <?= $this->element('metrics_report/highlight', array('metric' => 'highest_conversionRate_hour')) ?>                    
            <?= $this->element('metrics_report/highlight', array('metric' => 'highest_conversionRate_day')) ?>                                
            <?= $this->element('metrics_report/highlight', array('metric' => 'highest_transactions_hour')) ?>                                
            <?= $this->element('metrics_report/highlight', array('metric' => 'highest_transactions_day')) ?>                                            
        <?php } ?>
    </table>	
<?php } ?>
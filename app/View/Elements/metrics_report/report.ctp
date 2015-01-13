<table class="body-wrap">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">
            <div class="content">
                <table>
                    <tr>
                        <td>
                            <h3>Hi <?= $toName ?></h3>
                            <p></p>
                            <h3><?= $curRange ?></h3>
                            <p><em>Compared to <?= $prevRange ?></em></p>
                            <p></p>
                            <h5>Customer Activity</h5>		
                            <table class="metric" width="100%">
                                <?= $this->element('metrics_report/metric', array('metric'=>'walkbys')) ?>                                
                                <?= $this->element('metrics_report/metric', array('metric'=>'footTraffic')) ?>                                
                                <?= $this->element('metrics_report/metric', array('metric'=>'transactions')) ?>                                
                            </table>
                            <br/>
                            <h5>Conversion Rate</h5>
                            <table class="metric" width="100%">
                                <?= $this->element('metrics_report/metric', array('metric'=>'conversionRate')) ?>                                                                
                            </table>
                            <br/>
                            <h5>Customer Behavior</h5>
                            <table class="metric" width="100%">
                                <?= $this->element('metrics_report/metric', array('metric'=>'dwell')) ?>                                                                
                                <?= $this->element('metrics_report/metric', array('metric'=>'returning')) ?>                                                                                                                                                                                                                                                    
                            </table>
                            <p><br></p>
                            <?= $this->element('metrics_report/highlights') ?>                                                                                                                                                                                                                                                            
                            <?= $this->element('metrics_report/callout') ?>                                            
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
</table>
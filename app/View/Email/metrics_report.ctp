<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="viewport" content="width=device-width" />    
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Swarm <?= $reportType ?> Report</title>
    </head>
    <body>
        <?= $this->element('metrics_report/header') ?>        
        <?= $this->element('metrics_report/report') ?>            
        <?= $this->element('metrics_report/contact') ?>        
        <?= $this->element('metrics_report/footer') ?>        
    </body>
</html>
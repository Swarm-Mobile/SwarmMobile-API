<!DOCTYPE html>
<html lang="en">
    <head>        
        <?= $this->element('dashboard/meta-css')?>
        <?= $this->element('commons/mixpanel')?>
    </head>
    <body>
        <div id="wrapper">
            <?= $this->element('dashboard/header')?>            
            <?= $this->element('dashboard/sidebar')?>
            <div id="content">
                <div class="container-fluid">
                    <?php //echo $this->element('dashboard/maintenance')?>                    
                    <?= $this->element('dashboard/content-header')?>                    
                    <?= $this->fetch('content')?>
                </div>
            </div>
        </div>
        <?= $this->element('dashboard/setup-modal')?>
        <?= $this->element('dashboard/js')?>
    </body>
</html>
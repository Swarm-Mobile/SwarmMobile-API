<?php
$this->Html->css('bootstrap.3.min', null, array('inline' => false));
$this->Html->css('daterangepicker-bs3', null, array('inline' => false));
$this->Html->css('dashboard', null, array('inline' => false));
$this->Html->css('login', null, array('inline' => false));
$this->Html->script('bootstrap.3.min', array('inline' => false));
$this->assign('title', 'Swarm Mobile - API Login');
?>
<div id="wrapper">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="col-md-4 col-md-offset-4">
                <a href="<?= Router::url('/') ?>" class="logoDiv"></a>
            </div>
        </div>
        <div class="row-fluid">
            <div class="col-md-12 loginArea" style="text-align:center">
                <h2>Do you like to authorize your user <br/>to call our API?</h2>
                <?php
                echo $this->BootstrapForm->create('Authorize');
                foreach ($OAuthParams as $key => $value) {
                    echo $this->BootstrapForm->hidden(h($key), array('value' => h($value)));
                }
                ?>
                <input type="submit" name="accept" value="Yes" class="btn btn-lg btn-default" style="margin-right:25px"/>
                <input type="submit" name="accept" value="No" class="btn btn-lg btn-default"/>
                <?php echo $this->BootstrapForm->end(); ?>
            </div>
        </div>
        <div id="erorrDiv"></div>
    </div>
</div>

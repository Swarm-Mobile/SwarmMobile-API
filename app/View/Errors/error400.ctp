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
            <div class="row-fluid" style="text-align:center">
                <h2><?php echo $name; ?></h2>
                <p class="error">
                    <strong><?php echo __d('cake', 'Error'); ?>: </strong>
                    <?php
                    printf(
                            __d('cake', 'The requested address %s was not found on this server.'), "<strong>'{$url}'</strong>"
                    );
                    ?>
                </p>
                <?php
                if (Configure::read('debug') > 0):
                    echo $this->element('exception_stack_trace');
                endif;
                ?>
            </div>
        </div>        
    </div>
</div>

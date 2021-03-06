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
            <div class="col-md-4 col-md-offset-4 loginArea">
                <?php
                echo $this->Session->flash('auth');
                echo $this->BootstrapForm->create('User');
                foreach ($OAuthParams as $key => $value) {
                    echo $this->BootstrapForm->hidden(h($key), array('value' => h($value)));
                }
                //echo $this->BootstrapForm->input('username');
                //echo $this->BootstrapForm->input('password');
                ?>
                <div class="form-group required">
                    <label class="col-lg-2 control-label">Username</label>
                    <div class="col-lg-10">
                        <input name="username" 
                               class="form-control" 
                               type="text" 
                               required="required">
                    </div>
                </div>
                <div class="form-group required">
                    <label class="col-lg-2 control-label">Password</label>
                    <div class="col-lg-10">
                        <input name="password" 
                               class="form-control" 
                               type="password" 
                               id="UserPassword" 
                               required="required">
                    </div>
                </div>
                <button type="submit" class="btn btn-default pull-right caps" id="submitButton">Submit</button>
                <?= $this->BootstrapForm->end(); ?>
            </div>
        </div>
        <div id="erorrDiv"></div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $('form #submitButton').click(function(e) {
            e.preventDefault();
            if (checkInputs()) {
                $(this).parents('form').submit();
            }
        });
    });

    function checkInputs() {
        $("form input[required='required']").filter(function() {
            if (this.value) {
                $(this).parents('.form-group').removeClass("error");
            } else {
                $(this).parents('.form-group').addClass("error");
            }
        });
        errors = $('form .form-group.error').length;
        return !(errors != 0);
    }
</script>
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
            <div class="col-md-4 col-md-offset-4">
                <p class="white">
                    Congratulations! We receive your application form to
                    start to co-operate together making powerful applications
                    that consumes powerful data. In few days, we'll start 
                    a communication with you to talk a little bit more about
                    your project and how we can start to work together.
                </p>
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
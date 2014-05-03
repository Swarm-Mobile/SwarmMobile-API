<?php
$this->Html->css('bootstrap.3.min', null, array('inline' => false));
$this->Html->css('daterangepicker-bs3', null, array('inline' => false));
$this->Html->css('dashboard', null, array('inline' => false));
$this->Html->css('login', null, array('inline' => false));
$this->Html->script('bootstrap.3.min', array('inline' => false));
$this->assign('title', 'Swarm Mobile - Request OAuth Client');
?>
<div id="wrapper">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="col-md-4 col-md-offset-1">
                <a href="<?= Router::url('/') ?>" class="logoDiv"></a>
                <h2 class="white" style="text-align: center">Request OAuth Client</h2>
                <p class="white">
                    You like to create a new application using our data? You prefer
                    to update your current one to add a new source of data and
                    improve your knowledge? Swarm offer very good solutions to use
                    the data that we have stored in our servers. Fill the next form 
                    and, after a revision process, we will start a communication 
                    with you for create the best co-operation possible.
                </p>
            </div>
            <div class="col-md-6">
                <form style="padding-top:120px" class="form-horizontal" role="form" method="post" action="<?= Router::url('/request_client')?>">                    
                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label white">Email</label>
                        <div class="col-sm-9">
                            <input name="username" type="email" class="form-control" placeholder="email@domain.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="redirect_uri" class="col-sm-3 control-label white">Redirect URI</label>
                        <div class="col-sm-9">
                            <input name="redirect_uri" type="text" class="form-control" placeholder="http://www.mydomain.com/swarm_redirect_uri">
                        </div>
                    </div>
                    <div class="form-group">                        
                        <label for="description" class="col-sm-3 control-label white">Description</label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control" rows="4" placeholder="My application is awesome and likes to..."></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2 col-md-offset-3">
                            <button type="submit" class="btn btn-primary btn-xl">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
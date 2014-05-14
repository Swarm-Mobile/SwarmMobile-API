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
            <div class="col-md-10 col-md-offset-1 white">
                <a href="<?= Router::url('/') ?>" class="logoDiv"></a>
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <h2>How it works</h2>
                        <p class="white">
                            To start to use our API, you only need to get your first
                            pair of access_token/refresh token and start to make 
                            requests to our API on the different endpoints that you 
                            can find in the docummentation that we sent to you. Anything
                            more, anything less.
                        </p>
                        <h3>Get a Code</h3>
                        <code>
                            http://api.swarm-mobile.com/oauth/authorize?response_type=code&client_id=xxxx&redirect_url=xxxx
                        </code>
                        <h3>Get a Token</h3>
                        <code>
                            http://api.swarm-mobile.com/oauth/token?client_id=xxx&client_secret=xxx&code=xxx&grant_type=authorization_code&redirect_uri=xxxx
                        </code>
                        <h3>Refresh a Token</h3>
                        <code>
                            http://api.swarm-mobile.com/oauth/token?refresh_token=xxx&client_id=xxx&client_secret=xxx&grant_type=refresh_token&redirect_uri=xxxx
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
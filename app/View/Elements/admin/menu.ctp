<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= Router::url('/admin') ?>">Swarm API Management</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">                             
                <li><a href="<?= Router::url('/users/view') ?>">Users</a></li>  
                <li><a href="<?= Router::url('/inbox/view') ?>">Inbox</a></li>                 
            </ul>
            <ul clasS="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Links <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header">API Management</li>
                        <li><a target="_blank" href="<?= Router::url('/')?>">Request Client</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">swarm-mobile.com</li>
                        <li><a target="_blank" href="http://www.swarm-mobile.com">/website</a></li>
                        <li><a target="_blank" href="http://www.swarm-mobile.com/dashboard">/dashboard</a></li>
                        <li><a target="_blank" href="http://www.swarm-mobile.com/backstage">/dackstage</a></li>                        
                        <li><a target="_blank" href="http://www.swarm-mobile.com/console">/console</a></li>                        
                        <li class="divider"></li>
                        <li class="dropdown-header">others</li>
                        <li><a target="_blank" href="http://corp.swarm-mobile.com/wiki">Wiki</a></li>                        
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Actions <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= Router::url('/logout')?>">Logout</a></li>                        
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
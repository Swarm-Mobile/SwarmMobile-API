<?php

$oMenu = array(
    array('href'=>'store_overview'      , 'title'=>'Store Overview'     , 'color'=>8),
    array('href'=>''                    , 'title'=>'Dashboard'          , 'color'=>1),
    array('href'=>'store_analytics'     , 'title'=>'Store Analytics'    , 'color'=>2),
    array('href'=>'consumer_profiles'   , 'title'=>'Consumer Profiles'  , 'color'=>3),
    array('href'=>'network_analytics'   , 'title'=>'Network Analytics'  , 'color'=>4),
    array('href'=>'wifi_manager'        , 'title'=>'WI-FI Manager'      , 'color'=>7),
    array('href'=>'revenue_generator'   , 'title'=>'Revenue Generator'  , 'color'=>5),
    array('href'=>'settings'            , 'title'=>'Settings'           , 'color'=>8),
    array('href'=>'setup'               , 'title'=>'Setup'              , 'color'=>1),
);
?>
<div id="sidebar">
    <ul>
        <?php foreach($oMenu as $oRow){?>
        <li class="">
            <a href="/dashboard/<?= $oRow['href']?>" class="trans">
                <i>
                    <span class="nav-circle-outer trans color_border<?=$oRow['color']?>">
                        <span class="nav-circle-inner trans color_border<?=$oRow['color']?> color_bg<?=$oRow['color']?>"></span>
                    </span>
                </i>
                <?= $oRow['title'] ?>
            </a>
        </li>
        <?php } ?>
        <li class="visible-xs">
            <a href="{path='logout'}" class="trans">
                <i>
                    <span class="nav-circle-outer trans color_border8">
                        <span class="nav-circle-inner trans color_border8 color_bg8"></span>
                    </span>
                </i>
                Logout
            </a>
        </li>
    </ul>
</div>
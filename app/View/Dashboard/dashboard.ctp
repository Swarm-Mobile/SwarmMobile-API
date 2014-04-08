<?php 
    $this->assign('title','Dashboard');
?>
<div class="row">
    <div class="col-md-12 dataRow">
        <?= $this->element('dashboard/export-button')?>  
        <?= $this->element('dashboard/show-hide-metrics')?>
        <?= $this->element('dashboard/refresh-button')?>        
        <h2 class="headerSpacing">Key Metrics</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-3 dataRow metric Walkbys" data-title="Walkbys">
        <h5 class="primaryRegular caps text-center">Walkbys</h5>
        <div class="focusNumber text-center primaryBold">
            <i class="circleIcon hidden-xs color_bg1 footstepsIcon">
            </i>
            <span class="varA"></span>
        </div>
        <div class="progress">
            <div class="progress-bar color_bg1" role="progressbar"></div>
        </div>
        <div class="bottomMetrics">
            <div class="col-md-6 col-xs-6 subtle small varB"></div>
            <div class="col-md-6 col-xs-6 subtle small text-right change"></div>
        </div>
    </div>
    <div class="col-md-3 dataRow metric TotalShoppers" data-title="TotalShoppers">
        <h5 class="primaryRegular caps text-center">Total Shoppers</h5>
        <div class="focusNumber text-center primaryBold">
            <i class="circleIcon hidden-xs color_bg2 guestsIcon"></i>
            <span class="varA"></span>
        </div>
        <div class="progress">
            <div class="progress-bar color_bg2" role="progressbar"></div>
        </div>
        <div class="bottomMetrics">
            <div class="col-md-6 col-xs-6 subtle small varB"></div>
            <div class="col-md-6 col-xs-6 subtle small text-right change"></div>
        </div>
    </div>
    <div class="col-md-3 dataRow metric Transactions {if lightspeed_store_id}{if:else}hide{/if}"  data-title="Transactions">
        <h5 class="primaryRegular caps text-center">Transactions</h5>
        <div class="focusNumber text-center primaryBold">
            <i class="circleIcon hidden-xs color_bg3 tagIcon"></i>
            <span class="varA"></span>
        </div>
        <div class="progress">
            <div class="progress-bar color_bg3" role="progressbar"></div>
        </div>
        <div class="bottomMetrics">
            <div class="col-md-6 col-xs-6 subtle small varB"></div>
            <div class="col-md-6 col-xs-6 subtle small text-right change"></div>
        </div>
    </div>
    <div class="col-md-3 dataRow metric last Revenue {if lightspeed_store_id}{if:else}hide{/if}" data-title="Revenue">
        <h5 class="primaryRegular caps text-center">Revenue</h5>
        <div class="focusNumber text-center primaryBold">
            <i class="circleIcon hidden-xs color_bg4 revenueIcon"></i>
            <span class="varA"></span>
        </div>
        <div class="progress">
            <div class="progress-bar color_bg4" role="progressbar"></div>
        </div>
        <div class="bottomMetrics">
            <div class="col-md-6 col-xs-6 subtle small varB"></div>
            <div class="col-md-6 col-xs-6 subtle small text-right change percentString"></div>
        </div>
    </div>    
    <!--
    <div class="col-md-6 dataRow last ">
        <div class="alert-info noPosInfoWindow">
            <p class="text-center spacingTop">
                <strong>Your point of sale system is not currently integrated with your Swarm account.</strong>
            </p>
            <p class="text-center">
                <a href="mailto:info@swarm-mobile.com"> <strong>Contact Swarm Mobile to integrate your point of sale data and have access to conversion rates, revenue, transaction data, etc...</strong></a>
            </p>
        </div>
    </div>
    -->    
</div>
<div class="row">
    <div class="col-md-12 dataRow text-right">
        <a href="/store_analytics" 
           class="btn btn-primary black small rt-arrow-button btn-sm ">
            <span></span> view all store analytics 
        </a>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12 dataRow">
        <h2>Insights</h2>
    </div>
</div>
<div class="row spacing">
    <div class="col-md-3 dataRow">
        <div class="insight color_border1 WindowConversion" data-title="WindowConversion">
            <div class="primaryRegular caps small">Window Conversion</div>
            <div class="small subtle pull-right text-right change percentString"></div>
            <div class="varB hide"></div>
            <div class="secondaryNumber primaryBold varA"></div>
        </div>
    </div>
    <div class="col-md-3 dataRow">
        <div class="insight color_border2 ReturningShoppers" data-title="ReturningShoppers">
            <div class="primaryRegular caps small">Return Shoppers</div>
            <div class="small subtle pull-right text-right change"></div>
            <div class="varB hide"></div>
            <div class="secondaryNumber primaryBold varA"></div>
        </div>
        <div class="insight color_border2 AvgDwell visible-xs visible-sm" data-title="AvgDwell">
            <div class="primaryRegular caps small">Dwell Time</div>
            <div class="small subtle pull-right text-right change secondString"></div>
            <div class="varB hide"></div>
            <div class="secondaryNumber primaryBold varA"></div>
        </div>
    </div>
    <div class="col-md-3 dataRow">
        <div class="insight color_border3 ConversionRate {if lightspeed_store_id}{if:else}hide{/if}" data-title="ConversionRate">
            <div class="primaryRegular caps small">Conversion Rate</div>
            <div class="small subtle pull-right text-right change percentString"></div>
            <div class="varB hide"></div>
            <div class="secondaryNumber primaryBold varA"></div>
        </div>
    </div>
    <div class="col-md-3 dataRow last">
        <div class="insight color_border4 last AvgTicket {if lightspeed_store_id}{if:else}hide{/if}" data-title="AvgTicket">
            <div class="primaryRegular caps small">Average Ticket</div>
            <div class="small subtle pull-right text-right change"></div>
            <div class="varB hide"></div>
            <div class="secondaryNumber primaryBold varA"></div>
        </div>
        <div class="insight color_border4 last ItemsPerTransaction visible-xs visible-sm {if lightspeed_store_id}{if:else}hide{/if}" data-title="ItemsPerTransaction">
            <div class="primaryRegular caps small">Items / Transaction</div>
            <div class="small subtle pull-right text-right change"></div>
            <div class="varB hide"></div>
            <div class="secondaryNumber primaryBold varA"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 dataRow text-right">
        <a href="/store_analytics" 
           class="btn btn-primary black small rt-arrow-button btn-sm ">
            <span></span> view all store analytics 
        </a>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12 dataRow">
        <h2>Key Metric Graph</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12 dataRow">
        <div id="avc"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 dataRow text-right">
        <a href="/store_analytics" class="btn btn-primary black small rt-arrow-button btn-sm ">
            <span></span> view all store analytics 
        </a>
    </div>
</div>

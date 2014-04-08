<div id="header">
    <h1 class="stripSpacing"><a href="/dashboard">Swarm Mobile</a></h1>
    <a id="menu-trigger" href="#"><i class="glyphicon glyphicon-th-list"></i></a>
    <div id="user-nav">
        <ul class="btn-group">
            {if {exp:mx_members_lead:is_it_lead}}
            <li class="btn dropdown storesButton">
                <a href="#" data-toggle="dropdown" data-target="#menu-messages" class="white dropdown-toggle">
                    <i class="glyphicon glyphicon-home"></i> &nbsp;
                    <span class="text">{if segment_1=="stores"}Stores{if:else}{screen_name}{/if}</span> 
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu text-left" 
                    data-member="{member_id}" 
                    data-leader="{exp:mx_members_lead:lead_member_id}">
                    {exp:mx_members_lead:profile member_id="1"}
                    {if username!="{exp:mx_members_lead:lead_username}"}
                    <li>
                        <a href="#" 
                           data-member="{member_id}"
                           data-leader="{exp:mx_members_lead:lead_member_id}">
                            {screen_name}
                        </a>
                    </li>
                    {/if}
                    {if not_lead}{/if}
                    {/exp:mx_members_lead:profile}
                    <li class="divider"></li>
                    <li>
                        <a href="/stores" data-member="{exp:mx_members_lead:lead_member_id}">
                            Store Overview
                        </a>
                    </li>
                </ul>
            </li>
            <div class="logUserIn hide">
                {exp:mx_members_lead:form return="/login/redirect"}
                {members_list} 
                <input type="submit" value="Submit">
                {if not_lead}{/if} 
                {/exp:mx_members_lead:form}
            </div>
            {/if}
            <li class="btn">
                <a class="white" href="{path='logout'}">
                    <i class="glyphicon glyphicon-share-alt"></i> 
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
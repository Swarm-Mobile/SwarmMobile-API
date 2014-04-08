<!-- start Mixpanel -->
<!--
<script type="text/javascript">(function(e, b){if (!b.__SV){var a, f, i, g; window.mixpanel = b; b._i = []; b.init = function(a, e, d){function f(b, h){var a = h.split("."); 2 == a.length && (b = b[a[0]], h = a[1]); b[h] = function(){b.push([h].concat(Array.prototype.slice.call(arguments, 0)))}}var c = b; "undefined" !== typeof d?c = b[d] = []:d = "mixpanel"; c.people = c.people || []; c.toString = function(b){var a = "mixpanel"; "mixpanel" !== d && (a += "." + d); b || (a += " (stub)"); return a}; c.people.toString = function(){return c.toString(1) + ".people (stub)"}; i = "disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
            for (g = 0; g < i.length; g++)f(c, i[g]); b._i.push([a, e, d])}; b.__SV = 1.2; a = e.createElement("script"); a.type = "text/javascript"; a.async = !0; a.src = ("https:" === e.location.protocol?"https:":"http:") + '//cdn.mxpnl.com/libs/mixpanel-2.2.min.js'; f = e.getElementsByTagName("script")[0]; f.parentNode.insertBefore(a, f)}})(document, window.mixpanel || []);
            mixpanel.init("e0a393752db1eefc1c332eece383e5f5");</script>
<script type="text/javascript">
            //id the user with member id, compensate for global members
            mixpanel.identify('{if {exp:mx_members_lead:is_it_lead}}{exp:mx_members_lead:lead_member_id}{if:else}{member_id}{/if}');
            //register the user with their plan
            mixpanel.register({
            "username" : '{if "{exp:mx_members_lead:lead_username}"}{exp:mx_members_lead:lead_username}{if:else}{username}{/if}'
            });
            mixpanel.people.set({
            "plan": '{if {exp:mx_members_lead:is_it_lead}}Global Manager{if:else}Single Store{/if}',
                    "$name": "{if "{exp:mx_members_lead:lead_screen_name}"}{exp:mx_members_lead:lead_screen_name}{if:else}{screen_name}{/if}",
                    "$last_login": new Date()
            });
            if (!isAdmin){
                mixpanel.track("Dashboard Loaded");
            }
</script>
-->
<!-- end Mixpanel -->
<div class="btn-group pull-right hidden-xs userPreferencesFieldset hidden-print" data-cookie-class="visible-xs visible-sm" data-cookie-name="dashboard_filters" style="margin-left: 8px;">
    <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
        Show/Hide Metrics <span class="caret"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-right bigger-dropdown smaller-checkboxes">
        <div role="presentation" class="dropdown-header">Key Metrics:</div>
        <div class="form-group">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="Walkbys" checked disabled> Walkbys
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="TotalShoppers" checked disabled> Total Shoppers
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group {if lightspeed_store_id}{if:else}hide{/if}">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="Transactions" checked disabled> Transactions
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group {if lightspeed_store_id}{if:else}hide{/if}">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="Revenue" checked disabled> Revenue
                    </label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div role="presentation" class="divider"></div>
        <div role="presentation" class="dropdown-header">Insights:</div>
        <div class="form-group">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="WindowConversion"> Window Conversion
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="ReturningShoppers"> Return Visitors
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="AvgDwell"> Dwell Time
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group {if lightspeed_store_id}{if:else}hide{/if}">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="ConversionRate"> Conversion Rate
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group {if lightspeed_store_id}{if:else}hide{/if}">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="AvgTicket"> Average Ticket
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group {if lightspeed_store_id}{if:else}hide{/if}">
            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="ItemsPerTransaction"> Items / Transaction
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
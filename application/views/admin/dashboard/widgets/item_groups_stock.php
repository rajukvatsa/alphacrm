<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="Item Groups Stock">
    <div class="widget-dragger"></div>
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-header">
                <h4>Current Stock</h4>
            </div>
            <div id="item_groups_content">
                <ul class="nav nav-tabs" id="item_groups_tabs">
                    <li class="active"><a href="#loading_tab" data-toggle="tab">Loading...</a></li>
                </ul>
                <div class="tab-content" id="item_groups_tab_content">
                    <div class="tab-pane active" id="loading_tab">
                        <div class="text-center mtop15">
                            <i class="fa fa-spinner fa-spin"></i> Loading item groups...
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="mtop20">
                <h4>Pending Items Current Month</h4>
                <div id="pending_items_content">
                    <ul class="nav nav-tabs" id="pending_items_tabs">
                        <li class="active"><a href="#pending_loading_tab" data-toggle="tab">Loading...</a></li>
                    </ul>
                    <div class="tab-content" id="pending_items_tab_content">
                        <div class="tab-pane active" id="pending_loading_tab">
                            <div class="text-center mtop15">
                                <i class="fa fa-spinner fa-spin"></i> Loading pending items...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="mtop20">
                <h4>Paid Invoice Items Current Month</h4>
                <div id="paid_items_content">
                    <ul class="nav nav-tabs" id="paid_items_tabs">
                        <li class="active"><a href="#paid_loading_tab" data-toggle="tab">Loading...</a></li>
                    </ul>
                    <div class="tab-content" id="paid_items_tab_content">
                        <div class="tab-pane active" id="paid_loading_tab">
                            <div class="text-center mtop15">
                                <i class="fa fa-spinner fa-spin"></i> Loading paid items...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
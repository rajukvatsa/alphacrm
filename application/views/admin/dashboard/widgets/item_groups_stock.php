<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="Item Groups Stock">
    <div class="widget-dragger"></div>
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-header">
                <h4>Item Groups Stock</h4>
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
        </div>
    </div>
</div>


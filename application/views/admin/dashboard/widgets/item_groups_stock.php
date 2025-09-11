<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="Item Groups Stock">
    <div class="widget-dragger"></div>
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-header">
                <h4>Stock by Item Group</h4>
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
            <div class="mtop20">
                <h5>Pending Items Current Month</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th>Stock</th>
                                <th>Proposal Qty</th>
                                <th>Group</th>
                            </tr>
                        </thead>
                        <tbody id="open_proposals_table">
                            <tr><td colspan="4" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mtop20">
                <h5>Paid Invoice Items Current Month</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th>Stock</th>
                                <th>Sold Qty</th>
                                <th>Group</th>
                            </tr>
                        </thead>
                        <tbody id="paid_invoice_items_table">
                            <tr><td colspan="4" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


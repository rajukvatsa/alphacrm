<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('payment_totals'); ?>">
    <div class="widget-dragger"></div>
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-header">
                <h4 class="pull-left"><?php echo _l('payment_totals'); ?></h4>
                <div class="pull-right">
                    <select id="payment_filter" class="form-control input-sm" style="width:150px;">
                        <option value="january">January</option>
                        <option value="february">February</option>
                        <option value="march">March</option>
                        <option value="april">April</option>
                        <option value="may">May</option>
                        <option value="june">June</option>
                        <option value="july">July</option>
                        <option value="august">August</option>
                        <option value="september">September</option>
                        <option value="october">October</option>
                        <option value="november">November</option>
                        <option value="december">December</option>
                        <option value="this_week">This Week</option>
                        <option value="prev_week">Previous Week</option>
                        <option value="next_week">Next Week</option>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
            <div id="payment_totals_content">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Invoice ID</th>
                                <th>Amount</th>
                                
                                <th>Haulage Cost</th>
                                <th>Haulage Type</th>
                                <th>Payment Recieved</th>
                            </tr>
                        </thead>
                        <tbody id="payments_table_body">
                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


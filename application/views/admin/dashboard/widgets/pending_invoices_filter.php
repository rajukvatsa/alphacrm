<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="Pending Invoices Filter">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="widget-dragger"></div>
                    <div class="row">
                        <div class="col-md-8">
                            <h4 id="rajinvoices" class="no-margin">Pending Invoices & Items</h4>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="row">
                                
                                <div class="col-md-4">
                                    <select class="form-control input-sm" id="filter-type">
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
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control input-sm" id="start-date">
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control input-sm" id="end-date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mtop15">
                        <div id="pending-invoices-table">
                            <div class="text-center"><i class="fa fa-spinner fa-spin"></i></div>
                            <?php if(isset($invoices_pending) && count($invoices_pending) > 0){ ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead><tr>
                                        <th>Invoice #</th>
                                        <th>Item Description</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        
                                    </tr></thead>
                                    <tbody>
                                    <?php 
                                    $totalQty = 0;
                                    $totalAmount = 0;
                                    foreach($invoices_pending as $item){ ?>
                                        <tr>
                                            <td><?php echo $item['invoice_number']; ?></td>
                                            <td><?php echo $item['description']; ?></td>
                                            <td><?php echo $item['qty']; ?></td>
                                            <td><?php echo $item['rate'] ? $item['rate'] : 'No Category'; ?></td>
                                            
                                        </tr>
                                    <?php
                                        $totalQty += $item['qty'];  
                                        $totalAmount += $item['rate'];
                                         } ?>
                                         <tr class="total-row bg-red-500">
                                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                                            <td><strong><?php echo $totalQty; ?></strong></td>
                                            <td><strong><?php echo $totalAmount; ?></strong></td>
                                         </tr>
                                    </tbody>
                                </table>  
                                </div>
                            <?php } else { ?>
                                <p class="text-center text-muted">No pending invoices found</p>
                            <?php } ?>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


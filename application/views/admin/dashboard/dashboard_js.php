<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
var weekly_payments_statistics;
var monthly_payments_statistics;
var user_dashboard_visibility = <?php echo $user_dashboard_visibility; ?>;
$(function() {
    $("[data-container]").sortable({
        connectWith: "[data-container]",
        helper: 'clone',
        handle: '.widget-dragger',
        tolerance: 'pointer',
        forcePlaceholderSize: true,
        placeholder: 'placeholder-dashboard-widgets',
        start: function(event, ui) {
            $("body,#wrapper").addClass('noscroll');
            $('body').find('[data-container]').css('min-height', '20px');
        },
        stop: function(event, ui) {
            $("body,#wrapper").removeClass('noscroll');
            $('body').find('[data-container]').removeAttr('style');
        },
        update: function(event, ui) {
            if (this === ui.item.parent()[0]) {
                var data = {};
                $.each($("[data-container]"), function() {
                    var cId = $(this).attr('data-container');
                    data[cId] = $(this).sortable('toArray');
                    if (data[cId].length == 0) {
                        data[cId] = 'empty';
                    }
                });
                $.post(admin_url + 'staff/save_dashboard_widgets_order', data, "json");
            }
        }
    });

    // Read more for dashboard todo items
    $('.read-more').readmore({
        collapsedHeight: 150,
        moreLink: "<a href=\"#\"><?php echo _l('read_more'); ?></a>",
        lessLink: "<a href=\"#\"><?php echo _l('show_less'); ?></a>",
    });

    $('body').on('click', '#viewWidgetableArea', function(e) {
        e.preventDefault();

        if (!$(this).hasClass('preview')) {
            $(this).html("<?php echo _l('hide_widgetable_area'); ?>");
            $('[data-container]').append(
                '<div class="placeholder-dashboard-widgets pl-preview"></div>');
        } else {
            $(this).html("<?php echo _l('view_widgetable_area'); ?>");
            $('[data-container]').find('.pl-preview').remove();
        }

        $('[data-container]').toggleClass('preview-widgets');
        $(this).toggleClass('preview');
    });

    var $widgets = $('.widget');
    var widgetsOptionsHTML = '';
    widgetsOptionsHTML += '<div id="dashboard-options">';
    widgetsOptionsHTML +=
        "<div class=\"tw-flex tw-space-x-4 tw-items-center\"><h4 class='tw-font-medium tw-text-neutral-600 tw-text-lg'><i class='fa-regular fa-circle-question' data-toggle='tooltip' data-placement=\"bottom\" data-title=\"<?php echo _l('widgets_visibility_help_text'); ?>\"></i> <?php echo _l('widgets'); ?></h4><a href=\"<?php echo admin_url('staff/reset_dashboard'); ?>\" class=\"tw-text-sm\"><?php echo _l('reset_dashboard'); ?></a>";

    widgetsOptionsHTML +=
        ' <a href=\"#\" id="viewWidgetableArea" class=\"tw-text-sm\"><?php echo _l('view_widgetable_area'); ?></a></div>';

    $.each($widgets, function() {
        var widget = $(this);
        var widgetOptionsHTML = '';
        if (widget.data('name') && widget.html().trim().length > 0) {
            widgetOptionsHTML += '<div class="checkbox">';
            var wID = widget.attr('id');
            wID = wID.split('widget-');
            wID = wID[wID.length - 1];
            var checked = ' ';
            var db_result = $.grep(user_dashboard_visibility, function(e) {
                return e.id == wID;
            });
            if (db_result.length >= 0) {
                // no options saved or really visible
                if (typeof(db_result[0]) == 'undefined' || db_result[0]['visible'] == 1) {
                    checked = ' checked ';
                }
            }
            widgetOptionsHTML += '<input type="checkbox" class="widget-visibility" value="' + wID +
                '"' + checked + 'id="widget_option_' + wID + '" name="dashboard_widgets[' + wID + ']">';
            widgetOptionsHTML += '<label for="widget_option_' + wID + '">' + widget.data('name') +
                '</label>';
            widgetOptionsHTML += '</div>';
        }
        widgetsOptionsHTML += widgetOptionsHTML;
    });

    $('.screen-options-area').append(widgetsOptionsHTML);
    $('body').find('#dashboard-options input.widget-visibility').on('change', function() {
        if ($(this).prop('checked') == false) {
            $('#widget-' + $(this).val()).addClass('hide');
        } else {
            $('#widget-' + $(this).val()).removeClass('hide');
        }

        var data = {};
        var options = $('#dashboard-options input[type="checkbox"]').map(function() {
            return {
                id: this.value,
                visible: this.checked ? 1 : 0
            };
        }).get();

        data.widgets = options;
        /*
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
        */
        $.post(admin_url + 'staff/save_dashboard_widgets_visibility', data).fail(function(data) {
            // Demo usage, prevent multiple alerts
            if ($('body').find('.float-alert').length == 0) {
                alert_float('danger', data.responseText);
            }
        });
    });

    var tickets_chart_departments = $('#tickets-awaiting-reply-by-department');
    var tickets_chart_status = $('#tickets-awaiting-reply-by-status');
    var leads_chart = $('#leads_status_stats');
    var projects_chart = $('#projects_status_stats');

    if (tickets_chart_departments.length > 0) {
        // Tickets awaiting reply by department chart
        var tickets_dep_chart = new Chart(tickets_chart_departments, {
            type: 'doughnut',
            data: <?php echo $tickets_awaiting_reply_by_department; ?>,
        });
    }
    if (tickets_chart_status.length > 0) {
        // Tickets awaiting reply by department chart
        new Chart(tickets_chart_status, {
            type: 'doughnut',
            data: <?php echo $tickets_reply_by_status; ?>,
            options: {
                onClick: function(evt) {
                    onChartClickRedirect(evt, this);
                }
            },
        });
    }
    if (leads_chart.length > 0) {
        // Leads overview status
        new Chart(leads_chart, {
            type: 'doughnut',
            data: <?php echo $leads_status_stats; ?>,
            options: {
                maintainAspectRatio: false,
                onClick: function(evt) {
                    onChartClickRedirect(evt, this);
                }
            }
        });
    }
    if (projects_chart.length > 0) {
        // Projects statuses
        new Chart(projects_chart, {
            type: 'doughnut',
            data: <?php echo $projects_status_stats; ?>,
            options: {
                maintainAspectRatio: false,
                onClick: function(evt) {
                    onChartClickRedirect(evt, this);
                }
            }
        });
    }

    if ($(window).width() < 500) {
        // Fix for small devices weekly payment statistics
        $('#payment-statistics').attr('height', '250');
    }

    fix_user_data_widget_tabs();
    $(window).on('resize', function() {
        $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').removeAttr('style');
        fix_user_data_widget_tabs();
    });
    // Payments statistics
    init_weekly_payment_statistics(<?php echo $weekly_payment_stats; ?>);

    $('select[name="currency"]').on('change', function() {
        let $activeChart = $('#Payment-chart-name').data('active-chart');

        if (typeof(weekly_payments_statistics) !== 'undefined') {
            weekly_payments_statistics.destroy();
        }

        if (typeof(monthly_payments_statistics) !== 'undefined') {
            monthly_payments_statistics.destroy();
        }

        if ($activeChart == 'weekly') {
            init_weekly_payment_statistics();
        } else if ($activeChart == 'monthly') {
            init_monthly_payment_statistics();
        }

    });
});

function fix_user_data_widget_tabs() {
    if ((app.browser != 'firefox' &&
            isRTL == 'false' && is_mobile()) || (app.browser == 'firefox' &&
            isRTL == 'false' && is_mobile())) {
        $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').css('margin-bottom', '26px');
    }
}

function init_weekly_payment_statistics(data) {
    if ($('#payment-statistics').length > 0) {

        if (typeof(weekly_payments_statistics) !== 'undefined') {
            weekly_payments_statistics.destroy();
        }
        if (typeof(data) == 'undefined') {
            var currency = $('select[name="currency"]').val();
            $.get(admin_url + 'dashboard/weekly_payments_statistics/' + currency, function(response) {
                weekly_payments_statistics = new Chart($('#payment-statistics'), {
                    type: 'bar',
                    data: response,
                    options: {
                        responsive: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                }
                            }]
                        },
                    },
                });
            }, 'json');
        } else {
            weekly_payments_statistics = new Chart($('#payment-statistics'), {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    },
                },
            });
        }

    }
}

function init_monthly_payment_statistics() {
    if ($('#payment-statistics').length > 0) {

        if (typeof(monthly_payments_statistics) !== 'undefined') {
            monthly_payments_statistics.destroy();
        }

        var currency = $('select[name="currency"]').val();
        $.get(admin_url + 'dashboard/monthly_payments_statistics/' + currency, function(response) {
            monthly_payments_statistics = new Chart($('#payment-statistics'), {
                type: 'bar',
                data: response,
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    },
                },
            });
        }, 'json');
    }
}

function update_payment_statistics(el) {
    let type = $(el).data('type');
    let $chartNameWrapper = $('#Payment-chart-name');
    $chartNameWrapper.data('active-chart', type);
    $chartNameWrapper.text($(el).text());

    if (typeof(weekly_payments_statistics) !== 'undefined') {
        weekly_payments_statistics.destroy();
    }

    if (typeof(monthly_payments_statistics) !== 'undefined') {
        monthly_payments_statistics.destroy();
    }

    console.log(type);

    if (type == 'weekly') {
        init_weekly_payment_statistics();
    } else if (type == 'monthly') {
        init_monthly_payment_statistics();
    }

}

function update_tickets_report_table(el) {
    var $el = $(el);
    var type = $el.data('type')
    $('#tickets-report-mode-name').text($el.text())

    $('#tickets-report-table-wrapper').load(admin_url + 'dashboard/ticket_widget/' + type, function(data) {
        $('.table-ticket-reports').dataTable().fnDestroy()
        initDataTableInline('.table-ticket-reports')
    });
    return false
}

function loadPendingInvoices() {
    var filterType = $('#filter-type').val();
    var startDate = $('#start-date').val();
    var endDate = $('#end-date').val();
    $('#pending-invoices-table').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i></div>');
    
    $.get(admin_url + 'dashboard/pending_invoices_filter', {
        filter_type: filterType,
        start_date: startDate,
        end_date: endDate
    }, function(response) {
        var invoices = JSON.parse(response);
        var html = '';
        
        if (invoices.length > 0) {
            // Group by currency
            var groupedByCurrency = {};
            invoices.forEach(function(item) {
                var currency = item.currency_name || 'Unknown';
                if (!groupedByCurrency[currency]) {
                    groupedByCurrency[currency] = [];
                }
                groupedByCurrency[currency].push(item);
            });
            
            // Display each currency group
            Object.keys(groupedByCurrency).forEach(function(currency) {
                var items = groupedByCurrency[currency];
                html += '<h5 class="mtop15">Currency: ' + currency + '</h5>';
                html += '<div class="table-responsive">';
                html += '<table class="table table-striped">';
                html += '<thead><tr>';
                html += '<th>Invoice #</th>';
                html += '<th>Item Description</th>';
                html += '<th>Quantity</th>';
                html += '<th>Amount</th>';
                html += '</tr></thead><tbody>';
                
                var totalQty = 0;
                var totalAmount = 0;
                items.forEach(function(item) {
                    html += '<tr>';
                    html += '<td>' + item.invoice_number + '</td>';
                    html += '<td>' + item.description + '</td>';
                    html += '<td class="text-right">' +    Number(item.qty) + '</td>';
                    html += '<td class="text-right">' + (item.rate || '0.00') + '</td>';
                    html += '</tr>';
                    totalQty += parseFloat(item.qty);
                    totalAmount += parseFloat(item.rate || 0);
                });
                
                html += '<tr class="total-row success">';
                html += '<td colspan="2" class="text-right"><strong>Total </strong></td>';
                html += '<td class="text-right"><strong> Quantity:  ' + totalQty + '</strong></td>';
                html += '<td class="text-right"><strong>Amount: ' +currency+" " + totalAmount.toFixed(2) + '</strong></td>';
                html += '</tr>';
                html += '</tbody></table></div>';
            });
        } else {
            html = '<p class="text-center text-muted">No pending invoices found</p>';
        }
        
        $('#pending-invoices-table').html(html);
    });
}

$(document).ready(function() {
    var monthNames = ['january','february','march','april','may','june','july','august','september','october','november','december'];
    var currentMonth = monthNames[new Date().getMonth()];
   
    $('#filter-type').val(currentMonth);
    loadPendingInvoices();
    
    
});
$(document).on('change', '#filter-type', function() {
    loadPendingInvoices();
});

$(document).on('change', '#start-date, #end-date', function() {
    loadPendingInvoices();
});

</script>
<script>
$(document).ready(function() {
    var monthNames = ['january','february','march','april','may','june','july','august','september','october','november','december'];
    var currentMonth = monthNames[new Date().getMonth()];
    $('#payment_filter').val(currentMonth);
    loadPaymentTotals();
    
    $('#payment_filter').change(function() {
        loadPaymentTotals();
    });
});

function loadPaymentTotals() {
    var filter = $('#payment_filter').val();
    
    $.post(admin_url + 'dashboard/payment_totals_data', {
        filter: filter
    }, function(response) {
        var data = JSON.parse(response);
        var html = '';
        
        if (data.length > 0) {
            // Group by currency
            var groupedByCurrency = {};
            data.forEach(function(payment) {
                var currency = payment.currency_name || 'Unknown';
                if (!groupedByCurrency[currency]) {
                    groupedByCurrency[currency] = [];
                }
                groupedByCurrency[currency].push(payment);
            });
            
            // Display each currency group
            Object.keys(groupedByCurrency).forEach(function(currency) {
                var payments = groupedByCurrency[currency];
                html += '<tr class="info"><td colspan="5"><strong>Currency: ' + currency + '</strong></td></tr>';
                
                var totalAmount = 0;
                var totalHaulage = 0;
                
                payments.forEach(function(payment) {
                    html += '<tr>';
                    html += '<td>' + payment.invoiceid + '</td>';
                    html += '<td>' + payment.amount + '</td>';
                    html += '<td>' + (payment.haulage_cost || '-') + '</td>';
                    html += '<td>' + (payment.haulage_type || '-') + '</td>';
                    html += '<td>' + (payment.daterecorded || '-') + '</td>';
                    html += '</tr>';
                    
                    totalAmount += parseFloat(payment.amount);
                    totalHaulage += parseFloat(payment.haulage_cost) || 0;
                });
                
                html += '<tr class="success">';
                html += '<td class="text-right"><strong>Total</strong></td>';
                html += '<td><strong>' + currency + ' ' + totalAmount.toFixed(2) + '</strong></td>';
                html += '<td><strong>' + currency + ' ' + totalHaulage.toFixed(2) + '</strong></td>';
                html += '<td></td>';
                html += '<td></td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="5" class="text-center text-muted">No payments found</td></tr>';
        }
        
        $('#payments_table_body').html(html);
    });
}
</script>

<script>
$(document).ready(function() {
    var monthNames = ['january','february','march','april','may','june','july','august','september','october','november','december'];
    var currentMonth = monthNames[new Date().getMonth()];
    $('#stock_filter').val(currentMonth);
    loadStockData();
    
    $('#stock_filter').change(function() {
        loadStockData();
    });
});

function loadStockData() {
    var filter = $('#stock_filter').val();
    
    $.post(admin_url + 'dashboard/stock_manager_data', {
        filter: filter
    }, function(response) {
        var data = JSON.parse(response);
        
        // Paid Invoice Items
        var paidHtml = '';
        if (data.paid_items.length > 0) {
            data.paid_items.forEach(function(item) {
                paidHtml += '<tr>';
                paidHtml += '<td>' + item.description + '</td>';
                paidHtml += '<td>' + item.total_qty + '</td>';
                paidHtml += '</tr>';
            });
        } else {
            paidHtml = '<tr><td colspan="2" class="text-center text-muted">No items found</td></tr>';
        }
        $('#paid_invoice_items').html(paidHtml);
        
        // Proposal Items
        var proposalHtml = '';
        if (data.proposal_items.length > 0) {
            data.proposal_items.forEach(function(item) {
                proposalHtml += '<tr>';
                proposalHtml += '<td>' + item.description + '</td>';
                proposalHtml += '<td>' + item.total_qty + '</td>';
                proposalHtml += '</tr>';
            });
        } else {
            proposalHtml = '<tr><td colspan="2" class="text-center text-muted">No items found</td></tr>';
        }
        $('#proposal_items').html(proposalHtml);
    });
}
</script>
<script>
$(document).ready(function() {
    loadItemGroupsStock();
    loadOpenProposalsStock();
    loadPaidInvoiceItemsStock();
});

function loadOpenProposalsStock() {
    $.post(admin_url + 'dashboard/open_proposals_stock_data', function(response) {
        var data = JSON.parse(response);
        var html = '';
        
        if (data.length > 0) {
            data.forEach(function(item) {
                html += '<tr>';
                html += '<td>' + item.description + '</td>';
                html += '<td>' + (item.stock_in || '0') + '</td>';
                html += '<td>' + item.proposal_qty + '</td>';
                html += '<td>' + (item.group_name || 'No Group') + '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="4" class="text-center text-muted">No open proposal items found</td></tr>';
        }
        
        $('#open_proposals_table').html(html);
    });
}

function loadPaidInvoiceItemsStock() {
    $.post(admin_url + 'dashboard/paid_invoice_items_stock_data', function(response) {
        var data = JSON.parse(response);
        var html = '';
        
        if (data.length > 0) {
            data.forEach(function(item) {
                html += '<tr>';
                html += '<td>' + item.description + '</td>';
                html += '<td>' + (item.stock_in || '0') + '</td>';
                html += '<td>' + item.sold_qty + '</td>';
                html += '<td>' + (item.group_name || 'No Group') + '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="4" class="text-center text-muted">No paid invoice items found</td></tr>';
        }
        
        $('#paid_invoice_items_table').html(html);
    });
}

function loadItemGroupsStock() {
    $.post(admin_url + 'dashboard/item_groups_stock_data', {}, function(response) {
        var data = JSON.parse(response);
        
        var tabsHtml = '';
        var contentHtml = '';
        var firstTab = true;
        
        if (data.length > 0) {
           
            var totalStockValue = 0;
            data.forEach(function(group) {
                 var currentStockTotal = 0;
                var tabId = 'group_' + group.id;
                var activeClass = firstTab ? 'active' : '';
                
                // Tab header
                tabsHtml += '<li class="' + activeClass + '">';
                tabsHtml += '<a href="#' + tabId + '" data-toggle="tab">' + group.name + '</a>';
                tabsHtml += '</li>';
                
                // Tab content
                contentHtml += '<div class="tab-pane ' + activeClass + '" id="' + tabId + '">';
                contentHtml += '<div class="table-responsive mtop15">';
                contentHtml += '<table class="table table-striped">';
                contentHtml += '<thead><tr><th>Item Description</th><th>Current Stock</th><th>Pending Stock</th><th>Sold Stock</th></tr></thead>';
                contentHtml += '<tbody>';
                
                if (group.items.length > 0) {
                    group.items.forEach(function(item) {
                        contentHtml += '<tr>';
                        contentHtml += '<td>' + item.description + '</td>';
                        contentHtml += '<td>' + (item.stock_in || '0') + '</td>';
                        contentHtml += '<td>' + (item.stock_in || '0') + '</td>';
                        contentHtml += '<td>' + (item.stock_in || '0') + '</td>';
                        contentHtml += '</tr>';
                        currentStockTotal += parseFloat(item.stock_in || 0);
                      
                    });
                    contentHtml += '<tr class="total-row success">';
                    contentHtml += '<td colspan="1" class="text-right"><strong>Total Current Stock:</strong></td>';
                    contentHtml += '<td><strong>' + currentStockTotal + '</strong></td>';
                    contentHtml += '</tr>';
                    
                } else {
                    contentHtml += '<tr><td colspan="2" class="text-center text-muted">No items in this group</td></tr>';
                }
                
                contentHtml += '</tbody></table></div></div>';
                firstTab = false;
            });
        } else {
            tabsHtml = '<li class="active"><a href="#no_groups" data-toggle="tab">No Groups</a></li>';
            contentHtml = '<div class="tab-pane active" id="no_groups"><p class="text-center text-muted mtop15">No item groups found</p></div>';
        }
        
        $('#item_groups_tabs').html(tabsHtml);
        $('#item_groups_tab_content').html(contentHtml);
    });
}
</script>
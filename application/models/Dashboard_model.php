<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     * Used in home dashboard page
     * Return all upcoming events this week
     */
    public function get_upcoming_events()
    {
        $monday_this_week = date('Y-m-d', strtotime('monday this week'));
        $sunday_this_week = date('Y-m-d', strtotime('sunday this week'));

        $this->db->where("(start BETWEEN '$monday_this_week' and '$sunday_this_week')");
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');
        $this->db->order_by('start', 'desc');
        $this->db->limit(6);

        return $this->db->get(db_prefix() . 'events')->result_array();
    }

    /**
     * @param  integer (optional) Limit upcoming events
     * @return integer
     * Used in home dashboard page
     * Return total upcoming events next week
     */
    public function get_upcoming_events_next_week()
    {
        $monday_this_week = date('Y-m-d', strtotime('monday next week'));
        $sunday_this_week = date('Y-m-d', strtotime('sunday next week'));
        $this->db->where("(start BETWEEN '$monday_this_week' and '$sunday_this_week')");
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');

        return $this->db->count_all_results(db_prefix() . 'events');
    }

    /**
     * @param  mixed
     * @return array
     * Used in home dashboard page, currency passed from javascript (undefined or integer)
     * Displays weekly payment statistics (chart)
     */
    public function get_weekly_payments_statistics($currency)
    {
        $all_payments                 = [];
        $has_permission_payments_view = staff_can('view',  'payments');
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(' . db_prefix() . 'invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE)');
        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        // Current week
        $all_payments[] = $this->db->get()->result_array();
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(' . db_prefix() . 'invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY) ');

        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        // Last Week
        $all_payments[] = $this->db->get()->result_array();

        $chart = [
            'labels'   => get_weekdays(),
            'datasets' => [
                [
                    'label'           => _l('this_week_payments'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor'     => '#84c529',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
                [
                    'label'           => _l('last_week_payments'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor'     => '#c53da9',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
            ],
        ];


        for ($i = 0; $i < count($all_payments); $i++) {
            foreach ($all_payments[$i] as $payment) {
                $payment_day = date('l', strtotime($payment['date']));
                $x           = 0;
                foreach (get_weekdays_original() as $day) {
                    if ($payment_day == $day) {
                        $chart['datasets'][$i]['data'][$x] += $payment['amount'];
                    }
                    $x++;
                }
            }
        }

        return $chart;
    }


    /**
     * @param  mixed
     * @return array
     * Used in home dashboard page, currency passed from javascript (undefined or integer)
     * Displays monthly payment statistics (chart)
     */
    public function get_monthly_payments_statistics($currency)
    {
        $all_payments                 = [];
        $has_permission_payments_view = staff_can('view',  'payments');
        $this->db->select('SUM(amount) as total, MONTH(' . db_prefix() . 'invoicepaymentrecords.date) as month');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEAR(' . db_prefix() . 'invoicepaymentrecords.date) = YEAR(CURRENT_DATE)');
        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        $this->db->group_by('month');

        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        $all_payments = $this->db->get()->result_array();

        for ($i = 1; $i <= 12; $i++) {
            if (!isset($all_payments[$i])) {
                $all_payments[$i]['total'] = 0;
                $all_payments[$i]['month'] = $i;
            }
            $all_payments[$i]['label'] = _l(date("F", mktime(0, 0, 0, $i, 1)));
        }
        usort($all_payments, function($a, $b) {
            return (int) $a['month'] <=> (int) $b['month'];
        });

        $chart = [
            'labels'   => array_column($all_payments, 'label'),
            'datasets' => [
                [
                    'label'           => _l('report_sales_type_income'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor'     => '#84c529',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => array_column($all_payments, 'total'),
                ],
            ],
        ];
        return $chart;
    }

    public function projects_status_stats()
    {
        $this->load->model('projects_model');
        $statuses = $this->projects_model->get_project_statuses();
        $colors   = get_system_favourite_colors();

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];


        $has_permission = staff_can('view',  'projects');
        $sql            = '';
        foreach ($statuses as $status) {
            $sql .= ' SELECT COUNT(*) as total';
            $sql .= ' FROM ' . db_prefix() . 'projects';
            $sql .= ' WHERE status=' . $status['id'];
            if (!$has_permission) {
                $sql .= ' AND id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
            }
            $sql .= ' UNION ALL ';
            $sql = trim($sql);
        }

        $result = [];
        if ($sql != '') {
            // Remove the last UNION ALL
            $sql    = substr($sql, 0, -10);
            $result = $this->db->query($sql)->result();
        }

        foreach ($statuses as $key => $status) {
            array_push($_data['statusLink'], admin_url('projects?status=' . $status['id']));
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $result[$key]->total);
        }

        $chart['datasets'][]           = $_data;
        $chart['datasets'][0]['label'] = _l('home_stats_by_project_status');

        return $chart;
    }

    public function leads_status_stats()
    {
        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        $result = get_leads_summary();

        foreach ($result as $status) {
            if ($status['color'] == '') {
                $status['color'] = '#737373';
            }
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            if (!isset($status['junk']) && !isset($status['lost'])) {
                array_push($_data['statusLink'], admin_url('leads?status=' . $status['id']));
            }
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $status['total']);
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by department (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_department()
    {
        $this->load->model('departments_model');
        $departments = $this->departments_model->get();
        $colors      = get_system_favourite_colors();
        $chart       = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];

        $i = 0;
        foreach ($departments as $department) {
            if (!is_admin()) {
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    $departments_ids      = [];
                    if (count($staff_deparments_ids) == 0) {
                        $departments = $this->departments_model->get();
                        foreach ($departments as $department) {
                            array_push($departments_ids, $department['departmentid']);
                        }
                    } else {
                        $departments_ids = $staff_deparments_ids;
                    }
                    if (count($departments_ids) > 0) {
                        $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                    }
                }
            }
            $this->db->where_in('status', [
                1,
                2,
                4,
            ]);

            $this->db->where('department', $department['departmentid']);
            $this->db->where(db_prefix() . 'tickets.merged_ticket_id IS NULL', null, false);
            $total = $this->db->count_all_results(db_prefix() . 'tickets');

            if ($total > 0) {
                $color = '#333';
                if (isset($colors[$i])) {
                    $color = $colors[$i];
                }
                array_push($chart['labels'], $department['name']);
                array_push($_data['backgroundColor'], $color);
                array_push($_data['hoverBackgroundColor'], adjust_color_brightness($color, -20));
                array_push($_data['data'], $total);
            }
            $i++;
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by status (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_status()
    {
        $this->load->model('tickets_model');
        $statuses             = $this->tickets_model->get_ticket_status();
        $_statuses_with_reply = [
            1,
            2,
            4,
        ];

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        foreach ($statuses as $status) {
            if (in_array($status['ticketstatusid'], $_statuses_with_reply)) {
                if (!is_admin()) {
                    if (get_option('staff_access_only_assigned_departments') == 1) {
                        $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                        $departments_ids      = [];
                        if (count($staff_deparments_ids) == 0) {
                            $departments = $this->departments_model->get();
                            foreach ($departments as $department) {
                                array_push($departments_ids, $department['departmentid']);
                            }
                        } else {
                            $departments_ids = $staff_deparments_ids;
                        }
                        if (count($departments_ids) > 0) {
                            $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                        }
                    }
                }

                $this->db->where('status', $status['ticketstatusid']);
                $this->db->where(db_prefix() . 'tickets.merged_ticket_id IS NULL', null, false);
                $total = $this->db->count_all_results(db_prefix() . 'tickets');
                if ($total > 0) {
                    array_push($chart['labels'], ticket_status_translate($status['ticketstatusid']));
                    array_push($_data['statusLink'], admin_url('tickets/index/' . $status['ticketstatusid']));
                    array_push($_data['backgroundColor'], $status['statuscolor']);
                    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['statuscolor'], -20));
                    array_push($_data['data'], $total);
                }
            }
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    public function get_pending_invoices_filtered($filter_type, $start_date, $end_date)
    {
        $this->db->select('it.description,i.prefix, it.qty, i.number as invoice_number, it.rate, c.name as currency_name');
        $this->db->from(db_prefix() . 'invoices i');
        $this->db->join(db_prefix() . 'itemable it', 'it.rel_id = i.id AND it.rel_type = "invoice"');
        $this->db->join(db_prefix() . 'currencies c', 'c.id = i.currency');
        $this->db->where_in('i.status', [1, 4]); // Include both pending (1) and overdue (4) invoices
        
        $months = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
        ];
        
        if ($filter_type === 'custom') {
            if (!empty($start_date)) {
                $this->db->where('i.date >=', $start_date);
            }
            if (!empty($end_date)) {
                $this->db->where('i.date <=', $end_date);
            }
        } elseif (isset($months[$filter_type])) {
            $this->db->where('MONTH(i.date)', $months[$filter_type]);
            $this->db->where('YEAR(i.date)', date('Y'));
        } else {
            switch ($filter_type) {
                case 'this_week':
                    $this->db->where('YEARWEEK(i.date, 1) = YEARWEEK(CURDATE(), 1)');
                    break;
                case 'prev_week':
                    $this->db->where('YEARWEEK(i.date, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)');
                    break;
                case 'next_week':
                    $this->db->where('YEARWEEK(i.date, 1) = YEARWEEK(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), 1)');
                    break;
            }
        }
        
        $this->db->order_by('i.date', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_payment_totals($filter, $start_date = null, $end_date = null)
    {
        $this->db->select('p.invoiceid, i.number as invoice_number, ,i.prefix,p.amount, p.haulage_cost, p.haulage_type, p.daterecorded, c.symbol as currency_name');
        $this->db->from(db_prefix() . 'invoicepaymentrecords p');
        $this->db->join(db_prefix() . 'invoices i', 'i.id = p.invoiceid');
        $this->db->join(db_prefix() . 'currencies c', 'c.id = i.currency');
        
        $months = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
        ];
        
        if (isset($months[$filter])) {
            $this->db->where('MONTH(p.daterecorded)', $months[$filter]);
            $this->db->where('YEAR(p.daterecorded)', date('Y'));
        } elseif ($filter === 'this_week') {
            $this->db->where('YEARWEEK(p.daterecorded, 1) = YEARWEEK(CURDATE(), 1)');
        } elseif ($filter === 'prev_week') {
            $this->db->where('YEARWEEK(p.daterecorded, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)');
        } elseif ($filter === 'next_week') {
            $this->db->where('YEARWEEK(p.daterecorded, 1) = YEARWEEK(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), 1)');
        }
        
        $this->db->order_by('p.daterecorded', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_stock_manager_data($filter)
    {
        $months = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
        ];
        
        // Get paid invoice items
        $this->db->select('it.description, SUM(it.qty) as total_qty');
        $this->db->from(db_prefix() . 'invoices i');
        $this->db->join(db_prefix() . 'itemable ib', 'ib.rel_id = i.id AND ib.rel_type = "invoice"');
        $this->db->join(db_prefix() . 'items it', 'it.id = ib.item_id');
        $this->db->where('i.status', 2); // Paid status
        if (isset($months[$filter])) {
            $this->db->where('MONTH(i.date)', $months[$filter]);
            $this->db->where('YEAR(i.date)', date('Y'));
        }
        $this->db->group_by('it.description');
        $this->db->order_by('total_qty', 'DESC');
        $paid_items = $this->db->get()->result_array();
        
        // Get proposal items
        $this->db->select('it.description, SUM(it.qty) as total_qty');
        $this->db->from(db_prefix() . 'proposals p');
        $this->db->join(db_prefix() . 'itemable ib', 'ib.rel_id = p.id AND ib.rel_type = "proposal"');
        $this->db->join(db_prefix() . 'items it', 'it.id = ib.item_id');
        if (isset($months[$filter])) {
            $this->db->where('MONTH(p.date)', $months[$filter]);
            $this->db->where('YEAR(p.date)', date('Y'));
        }
        $this->db->group_by('it.description');
        $this->db->order_by('total_qty', 'DESC');
        $proposal_items = $this->db->get()->result_array();
        
        return [
            'paid_items' => $paid_items,
            'proposal_items' => $proposal_items
        ];
    }

    public function get_item_groups_stock_data($month = null)
    {
        // Get all item groups
        $this->db->select('id, name');
        $this->db->from(db_prefix() . 'items_groups');
        $this->db->order_by('name', 'ASC');
        $groups = $this->db->get()->result_array();
        
        // Get items for each group
        foreach ($groups as &$group) {
            $this->db->select('description, stock_in');
            $this->db->from(db_prefix() . 'items');
            $this->db->where('group_id', $group['id']);
            $this->db->order_by('description', 'ASC');
            $group['items'] = $this->db->get()->result_array();
        }
        
        return $groups;
    }

    public function get_open_proposals_stock_data($month = null)
    {
        $this->db->select('ib.description, i.stock_in, SUM(ib.qty) as proposal_qty, ig.name as group_name');
        $this->db->from(db_prefix() . 'proposals p');
        $this->db->join(db_prefix() . 'itemable ib', 'ib.rel_id = p.id AND ib.rel_type = "proposal"');
        $this->db->join(db_prefix() . 'items i', "i.description = LTRIM(SUBSTR(ib.description, INSTR(ib.description, '_') + 1))", 'left');
        $this->db->join(db_prefix() . 'items_groups ig', 'ig.id = i.group_id', 'left');
        $this->db->where_in('p.status', [1, 4]); // Open and Revised status

        $months = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
        ];
        if ($month && isset($months[$month])) {
            $this->db->where('MONTH(p.date)', $months[$month]);
            $this->db->where('YEAR(p.date)', date('Y'));
        } else {
            $this->db->where('MONTH(p.date)', date('n'));
            $this->db->where('YEAR(p.date)', date('Y'));
        }

        $this->db->group_by('ib.description');
        $this->db->order_by('ib.description', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_paid_invoice_items_stock_data($month = null)
    {
        $this->db->select('ib.description, i.stock_in, SUM(ib.qty) as sold_qty, ig.name as group_name');
        $this->db->from(db_prefix() . 'invoices inv');
        $this->db->join(db_prefix() . 'itemable ib', 'ib.rel_id = inv.id AND ib.rel_type = "invoice"');
         $this->db->join(db_prefix() . 'items i', "i.description = LTRIM(SUBSTR(ib.description, INSTR(ib.description, '_') + 1))", 'left');
        $this->db->join(db_prefix() . 'items_groups ig', 'ig.id = i.group_id', 'left');
        $this->db->where('inv.status', 2); // Paid status

        $months = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
        ];
        if ($month && isset($months[$month])) {
            $this->db->where('MONTH(inv.date)', $months[$month]);
            $this->db->where('YEAR(inv.date)', date('Y'));
        } else {
            $this->db->where('MONTH(inv.date)', date('n'));
            $this->db->where('YEAR(inv.date)', date('Y'));
        }

        $this->db->group_by('ib.description');
        $this->db->order_by('ib.description', 'ASC');
        return $this->db->get()->result_array();
    }
}

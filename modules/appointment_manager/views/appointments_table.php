<?php

defined('BASEPATH') or exit('No direct script access allowed');
$client = $this->ci->input->post('client_user_id');
if (!class_exists('appointment_manager_model')) {
    $this->ci->load('appointment_manager_model');
}
$statuses = $this->ci->appointment_manager_model->get_appointment_statuses();
$aColumns = [
    db_prefix() . 'appmgr_appointments.id as aptid',
    'company',
	db_prefix().'staff.firstname as stafffirstname',
	db_prefix().'staff.lastname as stafflastname',
    'appointee',
    'appointer',
    'appointment_date',
    'appointment_start_time',
    'appointment_end_time',
    db_prefix() . 'appmgr_locations.name as location_name',
    'status'
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'appmgr_appointments';
$where = [];
if ($client) {
    array_push($where, ' AND client =' . $client);
}
$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'appmgr_appointments.client',
    'LEFT JOIN ' . db_prefix() . 'appmgr_appointies ON ' . db_prefix() . 'appmgr_appointies.id = ' . db_prefix() . 'appmgr_appointments.appointee',
    'LEFT JOIN ' . db_prefix() . 'appmgr_locations ON ' . db_prefix() . 'appmgr_locations.id = ' . db_prefix() . 'appmgr_appointments.location',
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'appmgr_appointments.client',
    'LEFT JOIN ' . db_prefix() . 'staff as practitioners ON practitioners.staffid = ' . db_prefix() . 'appmgr_appointies.staff',
    'LEFT JOIN ' . db_prefix() . 'appmgr_appointee_availibility ON  ' . db_prefix() . 'appmgr_appointee_availibility.appointee_id= ' . db_prefix() . 'appmgr_appointments.appointee',
];
if (staff_can('view_own', 'appointment_manager')) {
    array_push($where, ' AND ' . db_prefix() . 'appmgr_appointments.appointer=' . get_staff_user_id());
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'staff.firstname as assigned_firstname', db_prefix() . 'staff.lastname as assigned_lastname', 'CONCAT(practitioners.firstname," ",practitioners.lastname) as practitioner', db_prefix() . 'appmgr_appointments.id', 'client', db_prefix() . 'appmgr_appointee_availibility.id as availid']);
$output = $result['output'];
$rResult = $result['rResult'];
$canEdit = staff_can('edit', 'appointment_manager');
$canDelete = staff_can('delete', 'appointment_manager');
foreach ($rResult as $aRow) {

    $row = [];
    $row[] = $aRow['aptid'];
    $nameRow = '<a href="' . admin_url('appointment_manager/appointee_availibility/' . $aRow['appointee'] . '/' . $aRow['availid']) . '">' . $aRow['practitioner'] . '</a>';
    $nameRow .= '<div class="row-options">';
    if ($canEdit) {
        $editFunction = 'edit_appointment(this,' . $aRow['id'] . '); return false;';
        if ($client) {
            $editFunction = 'edit_appointment_client(this,' . $aRow['id'] . '); return false;';
        }
        if ($client) {
            $nameRow .= '<a href="javascript:void(0);" data-href="' . admin_url('appointment_manager/appointment/' . $aRow['id']) . '" onclick="' . $editFunction . '" ><i class="fa-solid fa-pencil"></i></a>';
        } else {
            $nameRow .= '<a href="#" onclick="view_appointment(this,' . $aRow['id'] . '); return false; "><i class="fa-solid fa-eye"></i></a>' . '|' . '<a href="javascript:void(0);" data-href="' . admin_url('appointment_manager/appointment/' . $aRow['id']) . '" onclick="' . $editFunction . '" ><i class="fa-solid fa-pencil"></i></a>';
            $nameRow .= '| <a href="javascript:void(0);" data-href="' . admin_url('appointment_manager/project/' . $aRow['id']) . '" onclick="createTaProject(this);">' . _l('appmgr_create_project') . ' </a>';
        }
    }
    if ($canDelete) {
        $nameRow .= '| <a class="_delete text-danger" title="' . _l('appmgr_del_title') . '" href="' . admin_url('appointment_manager/del_appointment/' . $aRow['id']) . '"><i class="fa-solid fa-trash"></i></a>';
    }
    $nameRow .= '</div>';
    $row[] = $nameRow;
    $clientNameRow = '<a href="' . admin_url('clients/client/' . $aRow['client']) . '?group=appointment_manager">' . $aRow['stafffirstname']." ".$aRow['stafflastname'] . '</a>';
    $row[] = $clientNameRow;
    $row[] = _d($aRow['appointment_date']);
    $appointment_start_time = NULL;
    $appointment_end_time = NULL;
    if ($aRow['appointment_start_time']) {
        $appointment_start_time = date('h:i A', strtotime($aRow['appointment_start_time']));
    }
    if ($aRow['appointment_end_time']) {
        $appointment_end_time = date('h:i A', strtotime($aRow['appointment_end_time']));
    }
    $row[] = $appointment_start_time . _l('to') . $appointment_end_time;
    $row[] = timeDurationDiff($aRow['appointment_start_time'], $aRow['appointment_end_time']) . ' Hr';
    $row[] = $aRow['location_name'];
    $canChangeStatus = staff_can('approve', 'appointment_manager');
    $status = get_appointment_status_by_id($aRow['status']);
    $outputStatus = '';
    $outputStatus .= '<span title="' . e($status['name']) . '" class="label" style="color:' . $status['color'] . ';border:1px solid ' . adjust_hex_brightness($status['color'], 0.4) . ';background: ' . adjust_hex_brightness($status['color'], 0.04) . ';" task-status-table="' . e($aRow['status']) . '">';
    $outputStatus .= e($status['name']);
    if ($canChangeStatus) {
        $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaAppointmentStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $outputStatus .= '<span data-toggle="tooltip" title="' . _l('appmgr_single_change_status') . '"><i class="fa-solid fa-chevron-down tw-opacity-70"></i></span>';
        $outputStatus .= '</a>';
        $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $aRow['id'] . '">';
        foreach ($statuses as $changeStatus) {
            if ($aRow['status'] != $changeStatus['id']) {
                $outputStatus .= '<li>
                  <a href="#" onclick="appointment_mark_as(' . $changeStatus['id'] . ',' . $aRow['id'] . '); return false;">
                     ' . e(_l('appointment_mark_as', $changeStatus['name'])) . '
                  </a>
               </li>';
            }
        }
        $outputStatus .= '</ul>';
        $outputStatus .= '</div>';
    }
    $outputStatus .= '</span>';
    $row[] = $outputStatus;
    if ($aRow['appointer']) {
        $full_name = $aRow['assigned_firstname'] . ' ' . $aRow['assigned_lastname'];
        $assignedOutput = '<a data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $aRow['appointer']) . '">' . staff_profile_image($aRow['appointer'], [
            'staff-profile-image-small',
        ]) . '</a>';
        $assignedOutput .= '<span class="hide">' . $full_name . '</span>';
        $row[] = $assignedOutput;
    } else {
        $row[] = 'Public Form';
    }
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}

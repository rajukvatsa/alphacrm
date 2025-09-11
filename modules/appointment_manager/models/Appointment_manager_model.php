<?php

defined('BASEPATH') or exit('No direct script access allowed');

class appointment_manager_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_holiday($data)
    {
        $this->db->insert(db_prefix() . 'appmgr_holidays', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Holiday Added [ID:' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }
    public function update_holiday($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appmgr_holidays', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Holiday Updated [ID:' . $id . ']');
            return true;
        }
        return false;
    }
    public function get_holidays($calendar, $json = true)
    {
        $holidays = $this->db->get(db_prefix() . 'appmgr_holidays')->result_array();
        if ($json) {
            $json_data = [];
            if (isset($holidays) && !empty($holidays)) {
                foreach ($holidays as $holiday) {
                    if ($calendar == 'small') {
                        $json_data[] = $holiday['leave_date'];
                    } else {
                        $json_data[] = array('start' => $holiday['leave_date'], 'end' => $holiday['leave_date']);
                    }
                }
                return $json_data;
            }
        }
        return $holidays;
    }
    public function add_location($data)
    {
        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }
        $this->db->insert(db_prefix() . 'appmgr_locations', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            handle_tags_save($tags, $insert_id, 'appmgr_location');
            log_activity('New location Added [ID:' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }
    public function update_location($id, $data)
    {
        if (isset($data['tags'])) {
            handle_tags_save($data['tags'], $id, 'appmgr_location');
            unset($data['tags']);
        }
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appmgr_locations', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('location Updated [ID:' . $id . ']');
            return true;
        }
        return false;
    }
    public function get_locations($json = true)
    {
        $locations = $this->db->get(db_prefix() . 'appmgr_locations')->result_array();
        if ($json) {
            $json_data = [];
            if (isset($locations) && !empty($locations)) {
                foreach ($locations as $location) {
                    $json_data[] = array('start' => $location['leave_date'], 'end' => $location['leave_date']);
                }
                return $json_data;
            }
        }
        return $locations;
    }
    public function get_appointies($id = '', $where = [])
    {

        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'appmgr_appointies.id', $id);
            $this->db->join(db_prefix() . 'appmgr_appointee_availibility', db_prefix() . 'appmgr_appointee_availibility.appointee_id = ' . db_prefix() . 'appmgr_appointies.id', 'left');
            $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'appmgr_appointies.staff', 'left');
            $this->db->from(db_prefix() . 'appmgr_appointies');
            $this->db->select('*, CONCAT(' . db_prefix() . 'staff.firstname," ",' . db_prefix() . 'staff.lastname) as name,' . db_prefix() . 'appmgr_appointee_availibility.id as availid,' . db_prefix() . 'appmgr_appointies.id as appointeeid,' . db_prefix() . 'staff.staffid');
            return $this->db->get()->row();
        }
        return $this->db->get(db_prefix() . 'appmgr_appointies')->result_array();
    }
    public function update_appointee($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appmgr_appointies', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('appointee Updated [ID:' . $id . ']');
            return true;
        }
        return false;
    }
    public function add_appointee($data)
    {
        $this->db->insert(db_prefix() . 'appmgr_appointies', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New appointee Added [ID:' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }
    public function get_availibility($id = '', $where = [])
    {
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $unavailibility = $this->db->get(db_prefix() . 'appmgr_appointee_availibility')->row();
            return $unavailibility;
        }
        return $this->db->get(db_prefix() . 'appmgr_appointee_availibility')->result_array();
    }
    public function update_appointee_availibility($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appmgr_appointee_availibility', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('appointee availibility Updated [ID:' . $id . ']');
            return true;
        }
        return false;
    }
    public function add_appointee_availibility($data)
    {
        $this->db->insert(db_prefix() . 'appmgr_appointee_availibility', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('Appointee availibility Added [ID:' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }
    public function add_appointment($data, $public = false)
    {
        $data['appointer'] = get_staff_user_id();
        $data['added_at'] = date('Y-m-d H:i:s');
        $data['appointment_date'] = to_sql_date($data['appointment_date'], true);
        $data['appointment_start_time'] = date('H:i:s', strtotime($data['appointment_start_time']));
        $data['appointment_end_time'] = date('H:i:s', strtotime($data['appointment_end_time']));
        if (!isset($data['status'])) {
            $data['status'] = $this->appointment_manager_model->get_status_id_by_default();
        }

        $this->db->insert(db_prefix() . 'appmgr_appointments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (!$public) {
                $this->_send_appointment_notification_on_booked($insert_id, $data['status']);
            } else {
                $this->_send_appointment_notification_on_waiting_for_approval($insert_id, $data['status']);
            }
            log_activity('New Appointment Added [ID:' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }
    public function get_treatment($id = '', $where = [])
    {
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'appmgr_treatments')->row();
        }
        return $this->db->get(db_prefix() . 'appmgr_treatments')->result_array();
    }
    public function update_treatment($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appmgr_treatments', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Treatment Updated [ID:' . $id . ']');
            return true;
        }
        return false;
    }
    public function add_treatment($data)
    {
        $this->db->insert(db_prefix() . 'appmgr_treatments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Treatment Added [ID:' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }
    public function get_calendar_appointments($get_data)
    {
        $data = [];
        $start = $get_data['start'];
        $end = $get_data['end'];
        $practitionar = '';
        if (isset($get_data['practitionar'])) {
            $practitionar = $get_data['practitionar'];
        }
        $location = '';
        if (isset($get_data['location'])) {
            $location = $get_data['location'];
        }
        $timeFormat = "H:i";
        if (get_option('time_format') == 24) {
            $timeFormat = "H:i";
        } else {
            $timeFormat = "g:i A";
        }
        $is_admin = is_admin();
        $this->db->select(db_prefix() . 'appmgr_appointments.id,appointee,company,appointer,appointment_date,' . db_prefix() . 'appmgr_locations.name as locationname,' . db_prefix() . 'appmgr_appointments.description, CONCAT(' . db_prefix() . 'staff.firstname," ",' . db_prefix() . 'staff.lastname) as practitioner,tittle,color,appointment_start_time,appointment_end_time,customercolor');
        $this->db->where('(DATE(appointment_date) BETWEEN "' . $start . '" AND "' . $end . '")');
		
        if (is_numeric($practitionar)) {
            $this->db->where('appointee', $practitionar);
        }
        if (is_numeric($location)) {
            $this->db->where(db_prefix() . 'appmgr_appointments.location', $location);
        }
        if (!$is_admin) {
            $this->db->where('appointer', get_staff_user_id());
        }
		$this->db->where(db_prefix() . 'appmgr_appointments.status!=', "3");
		
		$this->db->where('appointer', get_staff_user_id());
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'appmgr_appointments.client', 'left');
        $this->db->join(db_prefix() . 'appmgr_treatments', db_prefix() . 'appmgr_treatments.id=' . db_prefix() . 'appmgr_appointments.treatment', 'left');
        $this->db->join(db_prefix() . 'appmgr_appointies', db_prefix() . 'appmgr_appointies.id=' . db_prefix() . 'appmgr_appointments.appointee', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'appmgr_appointies.staff', 'left');
        $this->db->join(db_prefix() . 'appmgr_locations', db_prefix() . 'appmgr_locations.id=' . db_prefix() . 'appmgr_appointments.location', 'left');
        $this->db->join(db_prefix() . 'appmgr_appointment_status', db_prefix() . 'appmgr_appointment_status.id=' . db_prefix() . 'appmgr_appointments.status', 'left');
        $result = $this->db->get(db_prefix() . 'appmgr_appointments')->result_array();
        if (isset($result) && !empty($result)) {
            foreach ($result as $event) {

                if ($event['appointer'] != get_staff_user_id() && !$is_admin) {
                    $event['is_not_creator'] = true;
                    $event['onclick'] = true;
                }
                $startDateTime = new DateTime($event['appointment_date'] . ' ' . $event['appointment_start_time']);
                $endDateTime = new DateTime($event['appointment_date'] . ' ' . $event['appointment_end_time']);
                $duration = $startDateTime->diff($endDateTime);
                $durationFormatted = $duration->h . ' hours ' . $duration->i . ' minutes';
                $_data['_tooltip'] = _l('calendar_') . ' - ' . $event['company'];
                $_data['color'] = $event['customercolor'];
                $_data['end'] = $event['appointment_date'] . ' ' . $event['appointment_end_time'];
                $_data['public'] = '';
                $_data['start'] = $event['appointment_date'] . ' ' . $event['appointment_start_time'];
                $_data['userid'] = $event['appointer'];
                $_data['eventid'] = $event['id'];
                $_data['title'] = $event['company'];
                $_data['client'] = $event['company'];
                $_data['practitioner'] = $event['practitioner'];
                $_data['description'] = $event['description'];
                $_data['location'] = $event['locationname'];
                $_data['treatment'] = $event['tittle'];
                $_data['rooms'] = $this->getOptedRooms($event['id']);
                $_data['time'] = _d($event['appointment_date']) . ' ' . date($timeFormat, strtotime($event['appointment_start_time']));
                $_data['duration'] = $durationFormatted;
                array_push($data, $_data);
            }
        }
        return $data;
    }
    public function get_appointment($id)
    {
        $timeFormat = get_option('time_format');
        $format = '"%H:%i"';
        if ($timeFormat === '24') {
            $format = '"%H:%i"';
        } else {
            $format = '"%h:%i %p"';
        }
        $this->db->select(
            db_prefix() . 'appmgr_appointments.*, ' . db_prefix() . 'appmgr_appointments.id aptid,' .
                db_prefix() . 'appmgr_locations.*, DATE_FORMAT(' .
                db_prefix() . 'appmgr_appointments.appointment_start_time, ' . $format . ') as startTime, DATE_FORMAT(' . db_prefix() . 'appmgr_appointments.appointment_end_time, ' . $format . ') as endTime,' . db_prefix() . 'appmgr_appointments.description as aptdesc,' . db_prefix() . 'appmgr_appointies.staff,'.db_prefix().'appmgr_appointments.description as aptdesc'
        );
        $this->db->join(
            db_prefix() . 'appmgr_locations',
            db_prefix() . 'appmgr_locations.id = ' . db_prefix() . 'appmgr_appointments.location',
            'left',
            db_prefix() . 'clients.userid = ' . db_prefix() . 'appmgr_appointments.client',
            'left'
        );
        $this->db->join(db_prefix() . 'appmgr_appointies', db_prefix() . 'appmgr_appointies.id=' . db_prefix() . 'appmgr_appointments.appointee', 'left');
        $this->db->where(db_prefix() . 'appmgr_appointments.id', $id);
        return $this->db->get(db_prefix() . 'appmgr_appointments')->row();
    }
  
    public function update_appointment($id, $data)
    {
        unset($data['isEdit']);
        $data['appointment_date'] = to_sql_date($data['appointment_date'], true);
        $data['appointer'] = get_staff_user_id();
        $data['added_at'] = date('Y-m-d H:i:s');
        $data['appointment_start_time'] = date('H:i:s', strtotime($data['appointment_start_time']));
        $data['appointment_end_time'] = date('H:i:s', strtotime($data['appointment_end_time']));
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appmgr_appointments', $data);
        if ($this->db->affected_rows() > 0) {
            if (!isset($data['status'])) {
                $app = $this->get_appointment_status_by_app_id($id);
                $data['status'] = $app->status;
            }
            $this->_send_appointment_to_client_notification($id, $data['status']);
            log_activity('Appointment Updated');
            return true;
        }
        return false;
    }
    public function delete_row($id, $table)
    {
        $this->db->where('id', $id);
        $this->db->delete($table);
        return $this->db->affected_rows();
    }
    public function get_holiday($id = '', $where = [])
    {
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'appmgr_holidays')->row();
        }
        return $this->db->get(db_prefix() . 'appmgr_holidays')->result_array();
    }
    public function get_location($id = '', $where = [])
    {
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'appmgr_locations')->row();
        }
        return $this->db->get(db_prefix() . 'appmgr_locations')->result_array();
    }
    public function get_appointment_statuses_static()
    {
        $statuses = [
            [
                'id' => 1,
                'color' => '#475569',
                'name' => _l('appmgr_appoint_status_1'),
                'order' => 1,
                'filter_default' => true,
            ],
            [
                'id' => 2,
                'color' => '#25eb36',
                'name' => _l('appmgr_appoint_status_2'),
                'order' => 2,
                'filter_default' => true,
            ],
            [
                'id' => 3,
                'color' => '#eb2525',
                'name' => _l('appmgr_appoint_status_3'),
                'order' => 3,
                'filter_default' => true,
            ],
        ];

        usort($statuses, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        return $statuses;
    }
    public function get_appointment_statuses($id = '', $where = [])
    {
        if (is_numeric($id)) {
            $this->db->where($where);
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'appmgr_appointment_status')->row();
        }

        $whereKey = md5(serialize($where));

        $statuses = $this->app_object_cache->get('ta-appointment-statuses-' . $whereKey);

        if (!$statuses) {
            $this->db->where($where);
            $this->db->order_by('statusorder', 'asc');

            $statuses = $this->db->get(db_prefix() . 'appmgr_appointment_status')->result_array();
            $this->app_object_cache->add('ta-appointment-statuses-' . $whereKey, $statuses);
        }

        return $statuses;
    }
    public function add_appointment_status($data)
    {
        if (isset($data['color']) && $data['color'] == '') {
            $data['color'] = hooks()->apply_filters('default_lead_status_coadd_appointment_statuslor', '#757575');
        }

        if (!isset($data['statusorder'])) {
            $data['statusorder'] = total_rows(db_prefix() . 'appmgr_appointment_status') + 1;
        }

        $this->db->insert(db_prefix() . 'appmgr_appointment_status', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Ta appointment Status Added [StatusID: ' . $insert_id . ', Name: ' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    public function update_appointment_status($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appmgr_appointment_status', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Ta appointment Status Updated [StatusID: ' . $id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }
    public function getcOptedRooms($locid = '', $appdate = '', $appid = '')
    {
        if (is_numeric($appid) && $appid) {

            $this->db->where('appointment_date', to_sql_date($appdate));
            $this->db->where('location', $locid);
            $this->db->where('appointee', $appid);
            $result = $this->db->get(db_prefix() . 'appmgr_appointments')->row();
            if (isset($result->opted_rooms)) {
                return $result->opted_rooms;
            }
        }
        return null;
    }


    public function getOptedRooms($appid)
    {
        if (is_numeric($appid) && $appid) {
            $this->db->where('id', $appid);
            return $this->db->get(db_prefix() . 'appmgr_appointments')->row()->opted_rooms;
        }
        return null;
    }

    public function get_other_calendar_appointments($data, $d_arr)
    {
        $is_admin = is_admin();
        $this->db->select(db_prefix() . 'appmgr_appointments.id,appointee,company,appointer,appointment_date,' . db_prefix() . 'appmgr_locations.name as locationname,' . db_prefix() . 'appmgr_appointments.description, CONCAT(' . db_prefix() . 'staff.firstname," ",' . db_prefix() . 'staff.lastname) as practitioner,tittle,color');
        $this->db->where('appointment_date BETWEEN "' . $d_arr['start'] . '" and "' . $d_arr['end'] . '"');
        if (!$is_admin) {
            $this->db->where('appointer', get_staff_user_id());
            $this->db->or_where('appointee', get_staff_user_id());
        }
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'appmgr_appointments.client', 'left');
        $this->db->join(db_prefix() . 'appmgr_treatments', db_prefix() . 'appmgr_treatments.id=' . db_prefix() . 'appmgr_appointments.treatment', 'left');
        $this->db->join(db_prefix() . 'appmgr_appointies', db_prefix() . 'appmgr_appointies.id=' . db_prefix() . 'appmgr_appointments.appointee', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'appmgr_appointies.staff', 'left');
        $this->db->join(db_prefix() . 'appmgr_locations', db_prefix() . 'appmgr_locations.id=' . db_prefix() . 'appmgr_appointments.location', 'left');
        $this->db->join(db_prefix() . 'appmgr_appointment_status', db_prefix() . 'appmgr_appointment_status.id=' . db_prefix() . 'appmgr_appointments.status', 'left');
        $result = $this->db->get(db_prefix() . 'appmgr_appointments')->result_array();
        if (isset($result) && !empty($result)) {
            foreach ($result as $event) {
                if ($event['appointer'] != get_staff_user_id() && !$is_admin) {
                    $event['is_not_creator'] = true;
                    $event['onclick'] = true;
                }
                $_data['_tooltip'] = _l('calendar_') . ' - ' . $event['company'];
                $_data['color'] = $event['color'];
                $_data['end'] = '';
                $_data['public'] = '';
                $_data['start'] = $event['appointment_date'];
                $_data['userid'] = $event['appointer'];
                $_data['eventid'] = $event['id'];
                $_data['title'] = $event['company'];
                $_data['client'] = $event['company'];
                $_data['practitioner'] = $event['practitioner'];
                $_data['description'] = $event['description'];
                $_data['location'] = $event['locationname'];
                $_data['treatment'] = $event['tittle'];
                $_data['rooms'] = $this->getOptedRooms($event['id']);
                $_data['time'] = _dt($event['appointment_date']);
                array_push($data, $_data);
            }
        }
        return $data;
    }
    public function getPractitionerBusyTimes($post_data)
    {
        if (!isset($post_data['location']) || (!isset($post_data['appointee']) && empty($post_data['appointee']))) {
            return json_encode(array('appointment_time' => '00:00', 'appointment_date' => '0000-00-00'));
        }
        $time_format = get_option('time_format');
        $format = '"%H:%i"';
        $time = '24';

        if ($time_format === '24') {
            $format = '"%H:%i"';
        } else {
            $time = '12';
            $format = '"%h:%i %p"';
        }
        $this->db->select('TIME_FORMAT(appointment_start_time, ' . $format . ') as appointment_time, DATE(appointment_date) as appointment_date');
        $this->db->from(db_prefix() . 'appmgr_appointments');
        $this->db->where(['location' => $post_data['location']]);
        $this->db->where(['appointee' => $post_data['appointee']]);
        $this->db->where(['DATE(appointment_date)' => date('Y-m-d', strtotime($post_data['appointment_date']))]);
        $dates = $this->db->get()->result_array();
        return json_encode($dates);
    }

    public function get_status_id_by_default()
    {
        $this->db->select('id');
        $this->db->from(db_prefix() . 'appmgr_appointment_status');
        $this->db->where('isdefault', 1);
        $result = $this->db->get()->row();
        if (isset($result->id)) {
            return $result->id;
        }
        return 0;
    }

    public function get_appointment_manager_status_by_name($name)
    {
        $this->db->where('name', $name);
        $this->db->from(db_prefix() . 'appmgr_appointment_status');
        return $this->db->get()->row();
    }


    public function mark_as($status, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appmgr_appointments', ['status' => $status]);
        if ($this->db->affected_rows() > 0) {
            $this->_send_appointment_to_client_notification($id, $status);
            return true;
        }
        return false;
    }

    private function _send_appointment_to_client_notification($appointmentId, $status)
    {
        $appointment = $this->get_appointment($appointmentId);
        $contact_id = get_primary_contact_user_id($appointment->client);
        $notifiedUsers = [];
        $notified = add_notification([
            'description' => $appointment->description,
            'touserid' => $appointment->appointee,
            'link' => '',
            'additional_data' => '',
        ]);
        if (!class_exists('clients_model')) {
            $this->load->model('clients_model');
        }
        $contact = $this->clients_model->get_contact($contact_id);
        if (isset($contact) && !empty($contact)) {
            $email_template = get_appmgr_email_template_by_status($status);
            if ($email_template != '') {
                send_mail_template($email_template, 'appointment_manager', $appointment, $contact);
            }
        }
        if ($notified) {
            array_push($notifiedUsers, $appointment->appointee);
            array_push($notifiedUsers, $appointment->appointer);
        }
        pusher_trigger_notification($notifiedUsers);
    }

    public function get_locations_id($location)
    {
        $this->db->where('LOWER(name)', strtolower($location));
        $query = $this->db->get(db_prefix() . 'appmgr_locations');

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return null;
        }
    }

    public function get_treatment_id($name)
    {
        $this->db->where('LOWER(tittle)', strtolower($name));
        $query = $this->db->get(db_prefix() . 'appmgr_treatments');

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return null;
        }
    }
    private function _send_appointment_notification_on_booked($appointmentId, $status)
    {
        $appointment = $this->get_appointment($appointmentId);
        $contact_id = get_primary_contact_user_id($appointment->client);
        $notifiedUsers = [];
        $notified = [];
        if (!empty($appointment->description)) {
            $notified = add_notification([
                'description' => $appointment->description,
                'touserid' => $appointment->appointee,
                'link' => '',
                'additional_data' => '',
            ]);
        }

        $staff = $this->staff_model->get($appointment->appointee);
        if (!class_exists('clients_model')) {
            $this->load->model('clients_model');
        }
        $email_template_client = 'appointment_manager_booked_email_send_to_client';
        $email_template_staff = 'appointment_manager_booked_email_send_to_staff';
        $contact = $this->clients_model->get_contact($contact_id);
        if (isset($contact) && !empty($contact)) {
            if ($email_template_client != '') {
                send_mail_template($email_template_client, 'appointment_manager', $appointment, $contact);
            }
        }
        if (isset($staff) && !empty($staff)) {
            if ($email_template_staff != '') {
                send_mail_template($email_template_staff, 'appointment_manager', $appointment, $staff);
            }
        }
        if ($notified) {
            array_push($notifiedUsers, $appointment->appointee);
            array_push($notifiedUsers, $appointment->appointer);
        }
        pusher_trigger_notification($notifiedUsers);
    }

    public function get_unregistered_staffs()
    {
        $select_str = '*,CONCAT(firstname,\' \',lastname) as full_name';
        $this->db->select($select_str);
        $this->db->from(db_prefix() . 'staff');
        $this->db->where('staffid NOT IN (SELECT staff FROM ' . db_prefix() . 'appmgr_appointies)');
        $this->db->order_by('firstname', 'desc');
        return $this->db->get()->result_array();
    }
    private function _send_appointment_notification_on_waiting_for_approval($appointmentId, $status)
    {
        $appointment = $this->get_appointment($appointmentId);
        $contact_id = get_primary_contact_user_id($appointment->client);
        $notifiedUsers = [];
        $notified = add_notification([
            'description' => $appointment->description,
            'touserid' => $appointment->appointee,
            'link' => '',
            'additional_data' => '',
        ]);
        $staff = $this->staff_model->get($appointment->appointee);
        if (!class_exists('clients_model')) {
            $this->load->model('clients_model');
        }
        $email_template_client = 'appointment_manager_waiting_for_approval_email_send_to_client';
        $email_template_staff = 'appointment_manager_waiting_for_approval_email_send_to_staff';
        $contact = $this->clients_model->get_contact($contact_id);
        if (isset($contact) && !empty($contact)) {
            if ($email_template_client != '') {
                send_mail_template($email_template_client, 'appointment_manager', $appointment, $contact);
            }
        }
        if (isset($staff) && !empty($staff)) {
            if ($email_template_staff != '') {
                send_mail_template($email_template_staff, 'appointment_manager', $appointment, $staff);
            }
        }
        if ($notified) {
            array_push($notifiedUsers, $appointment->appointee);
            array_push($notifiedUsers, $appointment->appointer);
        }
        pusher_trigger_notification($notifiedUsers);
    }
    public function get_practitioners($id = '', $where = [])
    {
        $this->db->where($where);
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'appmgr_appointies.staff', 'left');
        $this->db->from(db_prefix() . 'appmgr_appointies');
        $this->db->select('*, CONCAT(' . db_prefix() . 'staff.firstname,' . db_prefix() . 'staff.lastname) as name');
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get()->row();
        }
        return $this->db->get()->result_array();
    }
    public function add_appointee_unavailibility($data)
    {
        $this->db->insert(db_prefix() . 'appmgr_appointee_unavailibility', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('Appointee unavailibility Added [ID:' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }
    function get_unavailibilities($id = '', $appointeeId = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'appmgr_appointee_unavailibility')->row();
        }
        if (is_numeric($appointeeId)) {
            $this->db->where(['appointee_id' => $appointeeId]);
        }
        return $this->db->get(db_prefix() . 'appmgr_appointee_unavailibility')->result_array();
    }
    public function get_availibility_by_appointee_id($id = '')
    {
        $data = [];
        if (is_numeric($id)) {
            $this->db->where('appointee_id', $id);
            $data['availibility'] = $this->db->get(db_prefix() . 'appmgr_appointee_availibility')->row();
            $this->db->where('appointee_id', $id);
            $data['unavailibility'] = $this->db->get(db_prefix() . 'appmgr_appointee_unavailibility')->result_array();
            $data['holidays'] = $this->get_holidays('small');
            return $data;
        }
    }
    function get_appointment_status_by_app_id($id)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'appmgr_appointments')->row();
        }
        return 0;
    }
	
	public function check_duplicate($date, $starttime, $endtime)
    {
        $timeFormat = get_option('time_format');
        $format = '"%H:%i"';
        if ($timeFormat === '24') {
            $format = '"%H:%i"';
        } else {
            $format = '"%h:%i %p"';
        }
        $this->db->select(
            db_prefix() . 'appmgr_appointments.*, ' . db_prefix() . 'appmgr_appointments.id aptid'
        );
       $bookDate =date('Y-m-d', strtotime($date));
     
	  $this->db->where(db_prefix() . 'appmgr_appointments.appointment_date',"$bookDate");
	  $this->db->where(db_prefix() . 'appmgr_appointments.appointment_end_time >',$starttime);
	  $this->db->order_by("id", "desc");
	  $result=$this->db->get(db_prefix() . 'appmgr_appointments')->row();
	    $str = $this->db->last_query();

        return $result;
    }
}

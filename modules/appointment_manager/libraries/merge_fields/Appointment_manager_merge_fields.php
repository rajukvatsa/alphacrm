<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointment_manager_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Appointment Treatment',
                'key'       => '{treatment}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client'
                ],
            ],
            [
                'name'      => 'Appointment Description',
                'key'       => '{description}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ],
            [
                'name'      => 'Appointment Date',
                'key'       => '{appointment_date}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ],
            [
                'name'      => 'Appointment Start',
                'key'       => '{appointment_start_time}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ],
            [
                'name'      => 'Appointment End',
                'key'       => '{appointment_end_time}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ],
            [
                'name'      => 'Appointment Link',
                'key'       => '{appointment_link}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ],
            [
                'name'      => 'Appointment Rooms',
                'key'       => '{opted_rooms}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ],
            [
                'name'      => 'Location',
                'key'       => '{location_title}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ],
            [
                'name'      => 'Location Address',
                'key'       => '{location_detail}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ],
            [
                'name'      => 'Public URL',
                'key'       => '{public_url}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ],
            [
                'name'      => 'Status',
                'key'       => '{status}',
                'available' => [],
                'templates' => [
                    'appointment-manager-appointment-reminder-to-staff',
                    'appointment-manager-appointment-reminder-to-client',
                    'appointment-manager-appointment-status-change-to-client',
                    'appointment-manager-appointment-status-upcoming-to-client',
                    'appointment-manager-appointment-status-approved-to-client',
                    'appointment-manager-appointment-status-missed-to-client',
                    'appointment-manager-appointment-status-cancelled-to-client',
                    'appointment-manager-appointment-booked-to-client',
                    'appointment-manager-appointment-booked-to-staff',
                    'appointment-manager-appointment-wait-for-approval-to-client',
                    'appointment-manager-appointment-wait-for-approval-to-staff'
                ],
            ]
        ];
    }

    /**
     * Calendar event merge fields
     * @param  object $event event
     * @return array
     */
    public function format($event)
    {
        $fields = [];
        $fields['{appointment_date}']  = e(_dt($event->appointment_date));
        $fields['{event_link}']        = admin_url('utilities/calendar?eventid=' . $event->id);
        $this->ci->db->where('id', $event->location);
        $location = $this->ci->db->get(db_prefix() . 'appmgr_locations')->row();
        $fields['{location_title}']  = $location->name;
        $fields['{location_detail}']  = $location->description;
        $fields['{public_url}']  = site_url('appointment_manager/appointment_manager_client/public_form');
        $this->ci->db->where('id', $event->treatment);
        $treatment = $this->ci->db->get(db_prefix() . 'appmgr_treatments')->row();
        $fields['{treatment}']  = isset($treatment) && !empty($treatment) ? $treatment->tittle : '';
        $this->ci->db->where('id', $event->status);
        $status = $this->ci->db->get(db_prefix() . 'appmgr_appointment_status')->row();
        $fields['{status}']  = $status->name;
        return hooks()->apply_filters('appointment_manager_merge_fields', $fields, [
            'event' => $event,
        ]);
    }
}

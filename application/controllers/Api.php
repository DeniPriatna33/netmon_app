<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Device_model', 'device_model');
        $this->load->model('Alert_model', 'alert_model');
        $this->load->model('Monitoring_log_model', 'log_model');
    }

    /**
     * GET /api/get_dashboard_data
     * Returns JSON with summary stats, device list, and active alerts.
     */
    public function get_dashboard_data()
    {
        $summary = $this->device_model->get_dashboard_summary();
        $devices = $this->device_model->get_all();
        $alerts  = $this->alert_model->get_active_alerts();

        $this->json([
            'summary' => $summary,
            'devices' => $devices,
            'alerts'  => $alerts,
        ]);
    }

    /**
     * GET /api/get_device_chart/{device_id}
     * Returns last 30 monitoring_logs entries for the given device.
     */
    public function get_device_chart($device_id = NULL)
    {
        if ( ! $device_id) {
            $this->json([], 400);
            return;
        }

        $logs = $this->log_model->get_device_logs((int) $device_id, 30);
        $this->json($logs);
    }
}

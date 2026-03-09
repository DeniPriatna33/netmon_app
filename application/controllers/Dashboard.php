<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Device_model', 'device_model');
        $this->load->model('Alert_model', 'alert_model');
    }

    public function index()
    {
        $data['title']         = 'Dashboard';
        $data['refresh_ms']    = $this->config->item('dashboard_refresh_ms');
        $data['summary']       = $this->device_model->get_dashboard_summary();
        $data['active_alerts'] = $this->alert_model->get_active_alerts();
        $data['devices']       = $this->device_model->get_all();

        $this->render('dashboard/index', $data);
    }
}

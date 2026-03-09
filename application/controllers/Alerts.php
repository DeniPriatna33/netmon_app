<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alerts extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Alert_model', 'alert_model');
    }

    public function index()
    {
        $data['title'] = 'Alerts';
        $this->render('alerts/index', $data);
    }

    public function ajax_list()
    {
        $list = $this->alert_model->get_datatables();
        $data = [];
        $no   = isset($_POST['start']) ? $_POST['start'] : 0;

        foreach ($list as $row) {
            $no++;
            $resolved = $row->is_resolved
                ? '<span class="badge badge-success">Resolved</span>'
                : '<span class="badge badge-danger">Active</span>';

            $host_info = htmlspecialchars(($row->hostname ?? '') . ' (' . ($row->ip_address ?? '') . ')');

            $data[] = [
                $no,
                $host_info,
                htmlspecialchars($row->message),
                $resolved,
                $row->created_at,
                $row->resolved_at ?? '-',
            ];
        }

        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 0),
            'recordsTotal'    => $this->alert_model->count_all(),
            'recordsFiltered' => $this->alert_model->count_filtered(),
            'data'            => $data,
        ]);
    }
}

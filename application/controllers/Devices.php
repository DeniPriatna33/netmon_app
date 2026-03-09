<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Devices extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Device_model', 'device_model');
    }

    public function index()
    {
        $data['title'] = 'Daftar Perangkat';
        $this->render('devices/index', $data);
    }

    /**
     * DataTables server-side AJAX list.
     */
    public function ajax_list()
    {
        $list = $this->device_model->get_datatables();
        $data = [];
        $no   = isset($_POST['start']) ? $_POST['start'] : 0;

        foreach ($list as $row) {
            $no++;
            $status_badge = $row->status === 'UP'
                ? '<span class="badge badge-success">UP</span>'
                : ($row->status === 'DOWN'
                    ? '<span class="badge badge-danger">DOWN</span>'
                    : '<span class="badge badge-secondary">UNKNOWN</span>');

            $actions = '<button class="btn btn-xs btn-warning" onclick="edit(' . $row->id . ')"><i class="fas fa-edit"></i></button> ';
            $actions .= '<button class="btn btn-xs btn-danger" onclick="hapus(' . $row->id . ')"><i class="fas fa-trash"></i></button>';

            $data[] = [
                $no,
                htmlspecialchars($row->ip_address),
                htmlspecialchars($row->hostname),
                htmlspecialchars($row->description ?? ''),
                $status_badge,
                $actions,
            ];
        }

        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 0),
            'recordsTotal'    => $this->device_model->count_all(),
            'recordsFiltered' => $this->device_model->count_filtered(),
            'data'            => $data,
        ]);
    }

    /**
     * Get single device by ID (for edit modal).
     */
    public function ajax_get()
    {
        $id = $this->input->post('id');
        $row = $this->device_model->get_by_id($id);
        echo json_encode($row);
    }

    /**
     * Save (insert or update) device.
     */
    public function ajax_save()
    {
        $id = $this->input->post('id');

        $this->form_validation->set_rules('ip_address', 'IP Address', 'required|valid_ip');
        $this->form_validation->set_rules('hostname', 'Hostname', 'required|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'status' => FALSE,
                'errors' => $this->form_validation->error_array(),
            ]);
            return;
        }

        $ip = $this->input->post('ip_address', TRUE);

        // Check duplicate IP
        if ($this->device_model->ip_exists($ip, $id ?: NULL)) {
            echo json_encode([
                'status' => FALSE,
                'errors' => ['ip_address' => 'IP Address sudah terdaftar.'],
            ]);
            return;
        }

        $data = [
            'ip_address'  => $ip,
            'hostname'    => $this->input->post('hostname', TRUE),
            'description' => $this->input->post('description', TRUE),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($id) {
            $this->device_model->update($id, $data);
            $message = 'Perangkat berhasil diperbarui.';
        } else {
            $this->device_model->insert($data);
            $message = 'Perangkat berhasil ditambahkan.';
        }

        echo json_encode(['status' => TRUE, 'message' => $message]);
    }

    /**
     * Delete device.
     */
    public function ajax_delete()
    {
        $id = $this->input->post('id');
        $this->device_model->delete($id);
        echo json_encode(['status' => TRUE, 'message' => 'Perangkat berhasil dihapus.']);
    }
}

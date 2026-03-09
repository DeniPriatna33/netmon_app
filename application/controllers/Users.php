<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
    }

    public function index()
    {
        if ($this->require_admin() === FALSE) return;

        $data['title'] = 'Manajemen User';
        $this->render('users/index', $data);
    }

    public function ajax_list()
    {
        $list = $this->user_model->get_datatables();
        $data = [];
        $no   = isset($_POST['start']) ? $_POST['start'] : 0;

        foreach ($list as $row) {
            $no++;
            $active_badge = $row->is_active
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>';

            $actions = '<button class="btn btn-xs btn-warning" onclick="edit(' . $row->id . ')"><i class="fas fa-edit"></i></button> ';
            $actions .= '<button class="btn btn-xs btn-danger" onclick="hapus(' . $row->id . ')"><i class="fas fa-trash"></i></button>';

            $data[] = [
                $no,
                htmlspecialchars($row->username),
                htmlspecialchars($row->full_name ?? ''),
                htmlspecialchars($row->role),
                $active_badge,
                $actions,
            ];
        }

        // Set CSRF token in header for AJAX updates
        $this->output->set_header('X-CSRF-TOKEN: ' . $this->security->get_csrf_hash());
        
        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 0),
            'recordsTotal'    => $this->user_model->count_all(),
            'recordsFiltered' => $this->user_model->count_filtered(),
            'data'            => $data,
        ]);
    }

    public function ajax_get()
    {
        $id  = $this->input->post('id');
        $row = $this->user_model->get_by_id($id);
        if ($row) {
            unset($row->password_hash);
        }
        echo json_encode($row);
    }

    public function ajax_save()
    {
        $id = $this->input->post('id');

        $this->form_validation->set_rules('username', 'Username', 'required|min_length[3]|max_length[50]');
        $this->form_validation->set_rules('full_name', 'Nama Lengkap', 'required|max_length[100]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,operator]');

        if ( ! $id || empty($id)) {
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        } else {
            // For update, password is optional but must be min 6 chars if provided
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
        }

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'status' => FALSE,
                'errors' => $this->form_validation->error_array(),
            ]);
            return;
        }

        $username = $this->input->post('username', TRUE);

        if ($this->user_model->username_exists($username, $id ?: NULL)) {
            echo json_encode([
                'status' => FALSE,
                'errors' => ['username' => 'Username sudah digunakan.'],
            ]);
            return;
        }

        $data = [
            'username'  => $username,
            'full_name' => $this->input->post('full_name', TRUE),
            'role'      => $this->input->post('role', TRUE),
            'is_active' => $this->input->post('is_active') ? TRUE : FALSE,
            'updated_at'=> date('Y-m-d H:i:s'),
        ];

        $password = $this->input->post('password');
        if ( ! empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        // Add created_at for new users
        if ( ! $id || empty($id)) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        if ($id && ! empty($id)) {
            $this->user_model->update($id, $data);
            $message = 'User berhasil diperbarui.';
        } else {
            $this->user_model->insert($data);
            $message = 'User berhasil ditambahkan.';
        }

        echo json_encode(['status' => TRUE, 'message' => $message]);
    }

    public function ajax_delete()
    {
        $id = $this->input->post('id');

        // Prevent deleting own account
        if ($id == $this->session->userdata('user_id')) {
            echo json_encode(['status' => FALSE, 'message' => 'Tidak dapat menghapus akun sendiri.']);
            return;
        }

        $this->user_model->delete($id);
        echo json_encode(['status' => TRUE, 'message' => 'User berhasil dihapus.']);
    }
    
    /**
     * Get CSRF token for AJAX requests
     */
   public function get_csrf()
    {
        echo json_encode([
            'csrf_token_name' => $this->security->get_csrf_token_name(),
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
    }
}

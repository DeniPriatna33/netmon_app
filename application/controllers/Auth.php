<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
    }

    /**
     * Login page
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        $data['title'] = 'Login - Netmon';
        $this->render_auth('auth/login', $data);
    }

    /**
     * Process login form
     */
    public function process_login()
    {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Login - Netmon';
            $this->render_auth('auth/login', $data);
            return;
        }

        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password');

        $user = $this->user_model->authenticate($username, $password);

        if ($user) {
            $session_data = [
                'user_id'   => $user->id,
                'username'  => $user->username,
                'full_name' => $user->full_name,
                'role'      => $user->role,
                'logged_in' => TRUE,
            ];
            $this->session->set_userdata($session_data);
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'Username atau password salah.');
            redirect('auth/login');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}

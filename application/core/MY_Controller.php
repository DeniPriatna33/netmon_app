<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->_check_login();
    }

    /**
     * Redirect to login if user has no active session.
     * Skipped for Auth controller so login page is accessible.
     */
    private function _check_login()
    {
        $excluded = ['auth', 'cli_monitor', 'api'];
        $controller = $this->router->class;

        if ( ! in_array($controller, $excluded)) {
            if ( ! $this->session->userdata('user_id')) {
                redirect('auth/login');
            }
            // Populate common user data for views
            $this->data['current_user'] = (object) [
                'id'        => $this->session->userdata('user_id'),
                'username'  => $this->session->userdata('username'),
                'full_name' => $this->session->userdata('full_name'),
                'role'      => $this->session->userdata('role'),
            ];
        }
    }

    /**
     * Render full template: Header + Sidebar + Content + Footer
     */
    public function render($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        $data['title'] = $data['title'] ?? 'Netmon';

        $this->load->view('templates/header', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Render without sidebar (for login/register pages)
     */
    public function render_auth($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        $this->load->view('templates/auth_header', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/auth_footer', $data);
    }

    /**
     * Return JSON response (for AJAX endpoints)
     */
    protected function json($data, $status_code = 200)
    {
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /**
     * Require admin role. Redirect or return 403 if not admin.
     */
    protected function require_admin()
    {
        if ($this->session->userdata('role') !== 'admin') {
            if ($this->input->is_ajax_request()) {
                $this->json(['status' => FALSE, 'message' => 'Akses ditolak'], 403);
                return FALSE;
            }
            $this->session->set_flashdata('error', 'Akses ditolak. Anda bukan admin.');
            redirect('dashboard');
        }
        return TRUE;
    }
}

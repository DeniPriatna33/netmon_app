<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CLI-only controller for database seeding and migration.
 *
 * Usage:
 *   php index.php migrate current     -> Run migrations to current version
 *   php index.php seed run            -> Seed initial superadmin user
 */
class Seed extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        if ( ! is_cli()) {
            show_error('Akses hanya via CLI.', 403);
        }
    }

    /**
     * Seed the initial superadmin user.
     */
    public function run()
    {
        $this->load->model('User_model', 'user_model');

        // Check if any user exists
        $existing = $this->db->count_all('users');
        if ($existing > 0) {
            echo "Users already exist ({$existing} row(s)). Skipping seed.\n";
            return;
        }

        $data = [
            'username'      => 'admin',
            'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
            'full_name'     => 'Super Admin',
            'role'          => 'admin',
            'is_active'     => TRUE,
        ];

        $this->user_model->insert($data);
        echo "Superadmin created successfully.\n";
        echo "  Username : admin\n";
        echo "  Password : admin123\n";
        echo "  (Change this password immediately after first login!)\n";
    }
}

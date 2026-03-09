<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {

    protected $table       = 'users';
    protected $primary_key = 'id';
    protected $fillable    = ['username', 'password_hash', 'full_name', 'role', 'is_active', 'created_at', 'updated_at'];
    protected $searchable  = ['username', 'full_name', 'role'];
    protected $orderable   = ['', 'username', 'full_name', 'role', 'is_active', 'created_at'];

    protected $validation_rules = [
        ['field' => 'username',  'label' => 'Username',  'rules' => 'required|min_length[3]|max_length[50]'],
        ['field' => 'full_name', 'label' => 'Nama Lengkap', 'rules' => 'required|max_length[100]'],
        ['field' => 'role',      'label' => 'Role',      'rules' => 'required|in_list[admin,operator]'],
    ];

    /**
     * Authenticate user by username & password.
     * Returns user object or FALSE.
     */
    public function authenticate($username, $password)
    {
        $user = $this->db->get_where($this->table, [
            'username'  => $username,
            'is_active' => TRUE,
        ])->row();

        if ($user && password_verify($password, $user->password_hash)) {
            return $user;
        }
        return FALSE;
    }

    /**
     * Check if username already exists (for validation).
     */
    public function username_exists($username, $exclude_id = NULL)
    {
        $this->db->where('username', $username);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }
}

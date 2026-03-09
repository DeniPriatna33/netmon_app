<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Device_model extends MY_Model {

    protected $table       = 'devices';
    protected $primary_key = 'id';
    protected $fillable    = ['ip_address', 'hostname', 'description', 'status', 'ping_fail_count', 'is_active', 'updated_at'];
    protected $searchable  = ['ip_address', 'hostname', 'description'];
    protected $orderable   = ['', 'ip_address', 'hostname', 'description', 'status', ''];

    protected $validation_rules = [
        ['field' => 'ip_address', 'label' => 'IP Address', 'rules' => 'required|valid_ip|max_length[45]'],
        ['field' => 'hostname',   'label' => 'Hostname',   'rules' => 'required|max_length[100]'],
        ['field' => 'description','label' => 'Deskripsi',  'rules' => 'max_length[500]'],
    ];

    /**
     * Get all active devices for polling.
     */
    public function get_active_devices()
    {
        return $this->db->get_where($this->table, ['is_active' => TRUE])->result();
    }

    /**
     * Get device count by status.
     */
    public function count_by_status($status)
    {
        return $this->db->where('status', $status)->count_all_results($this->table);
    }

    /**
     * Get summary counts for dashboard.
     */
    public function get_dashboard_summary()
    {
        $total   = $this->db->count_all($this->table);
        $up      = $this->count_by_status('UP');
        $down    = $this->count_by_status('DOWN');
        $unknown = $this->count_by_status('UNKNOWN');

        return [
            'total'   => $total,
            'up'      => $up,
            'down'    => $down,
            'unknown' => $unknown,
        ];
    }

    /**
     * Check if IP already exists (for validation).
     */
    public function ip_exists($ip, $exclude_id = NULL)
    {
        $this->db->where('ip_address', $ip);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alert_model extends MY_Model {

    protected $table       = 'alerts';
    protected $primary_key = 'id';
    protected $fillable    = ['device_id', 'message', 'is_resolved', 'resolved_at'];
    protected $searchable  = ['message'];
    protected $orderable   = ['', 'device_id', 'message', 'is_resolved', 'created_at', ''];

    /**
     * Get active (unresolved) alerts with device info.
     */
    public function get_active_alerts()
    {
        return $this->db
            ->select('alerts.*, devices.ip_address, devices.hostname')
            ->from($this->table)
            ->join('devices', 'devices.id = alerts.device_id', 'left')
            ->where('alerts.is_resolved', FALSE)
            ->order_by('alerts.created_at', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Get all alerts with device info (for DataTables).
     */
    protected function _datatable_select()
    {
        $this->db->select('alerts.*, devices.ip_address, devices.hostname');
    }

    protected function _datatable_join()
    {
        $this->db->join('devices', 'devices.id = alerts.device_id', 'left');
    }

    /**
     * Count active (unresolved) alerts.
     */
    public function count_active()
    {
        return $this->db
            ->where('is_resolved', FALSE)
            ->count_all_results($this->table);
    }

    /**
     * Resolve all alerts for a specific device.
     */
    public function resolve_by_device($device_id)
    {
        $this->db->where('device_id', $device_id);
        $this->db->where('is_resolved', FALSE);
        return $this->db->update($this->table, [
            'is_resolved' => TRUE,
            'resolved_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Check if device has an unresolved alert.
     */
    public function has_active_alert($device_id)
    {
        return $this->db
            ->where('device_id', $device_id)
            ->where('is_resolved', FALSE)
            ->count_all_results($this->table) > 0;
    }
}

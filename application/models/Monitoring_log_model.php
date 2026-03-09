<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring_log_model extends MY_Model {

    protected $table       = 'monitoring_logs';
    protected $primary_key = 'id';
    protected $fillable    = ['device_id', 'status_up', 'ping_latency_ms', 'recorded_at'];

    /**
     * Get latest N logs for a specific device (for charts).
     */
    public function get_device_logs($device_id, $limit = 30)
    {
        return $this->db
            ->where('device_id', $device_id)
            ->order_by('recorded_at', 'DESC')
            ->limit($limit)
            ->get($this->table)
            ->result();
    }

    /**
     * Get the most recent log for each device (for dashboard list).
     */
    public function get_latest_per_device()
    {
        $sql = "
            SELECT ml.*, d.ip_address, d.hostname
            FROM monitoring_logs ml
            INNER JOIN (
                SELECT device_id, MAX(recorded_at) AS max_recorded
                FROM monitoring_logs
                GROUP BY device_id
            ) latest ON ml.device_id = latest.device_id AND ml.recorded_at = latest.max_recorded
            INNER JOIN devices d ON d.id = ml.device_id
            ORDER BY d.hostname ASC
        ";
        return $this->db->query($sql)->result();
    }

    /**
     * Delete logs older than specified days (housekeeping).
     */
    public function cleanup_old_logs($days = 7)
    {
        $sql = "DELETE FROM {$this->table} WHERE recorded_at < NOW() - INTERVAL '{$days} days'";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }
}

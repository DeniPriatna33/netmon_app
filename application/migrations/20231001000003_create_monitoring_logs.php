<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_monitoring_logs extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'SERIAL',
            ],
            'device_id' => [
                'type' => 'INT',
            ],
            'status_up' => [
                'type' => 'BOOLEAN',
            ],
            'ping_latency_ms' => [
                'type' => 'FLOAT',
                'null' => TRUE,
            ],
            'recorded_at' => [
                'type'    => 'TIMESTAMP',
                'default' => 'NOW()',
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('monitoring_logs');

        $this->db->query('CREATE INDEX idx_monlogs_device ON monitoring_logs(device_id)');
        $this->db->query('CREATE INDEX idx_monlogs_recorded ON monitoring_logs(recorded_at)');
        $this->db->query('ALTER TABLE monitoring_logs ADD CONSTRAINT fk_monlogs_device FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->dbforge->drop_table('monitoring_logs', TRUE);
    }
}

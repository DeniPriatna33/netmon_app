<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_alerts extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'SERIAL',
            ],
            'device_id' => [
                'type' => 'INT',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'is_resolved' => [
                'type'    => 'BOOLEAN',
                'default' => 'FALSE',
            ],
            'resolved_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => 'NOW()',
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('alerts');

        $this->db->query('CREATE INDEX idx_alerts_device ON alerts(device_id)');
        $this->db->query('CREATE INDEX idx_alerts_resolved ON alerts(is_resolved)');
        $this->db->query('ALTER TABLE alerts ADD CONSTRAINT fk_alerts_device FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->dbforge->drop_table('alerts', TRUE);
    }
}

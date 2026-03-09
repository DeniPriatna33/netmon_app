<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_devices extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'SERIAL',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
            ],
            'hostname' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => "'UNKNOWN'",
            ],
            'ping_fail_count' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => 'TRUE',
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => 'NOW()',
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('devices');

        $this->db->query('CREATE UNIQUE INDEX idx_devices_ip ON devices(ip_address)');
    }

    public function down()
    {
        $this->dbforge->drop_table('devices', TRUE);
    }
}

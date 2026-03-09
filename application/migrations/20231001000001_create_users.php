<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_users extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'SERIAL',
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => TRUE,
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'operator',
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
        $this->dbforge->create_table('users');

        $this->db->query('CREATE UNIQUE INDEX idx_users_username ON users(username)');
    }

    public function down()
    {
        $this->dbforge->drop_table('users', TRUE);
    }
}

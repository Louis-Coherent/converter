<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFilesTable extends Migration
{
    public function up()
    {
        // Create the 'files' table
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'file_name'   => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'file_path'   => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'status'      => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'pending',
            ],
            'unique_id'   => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,  // Ensure unique IDs
            ],
        ]);

        // Primary Key
        $this->forge->addPrimaryKey('id');

        // Create the table
        $this->forge->createTable('files');
    }

    public function down()
    {
        // Drop the 'files' table if rolling back the migration
        $this->forge->dropTable('files');
    }
}

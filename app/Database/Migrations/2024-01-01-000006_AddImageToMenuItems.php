<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageToMenuItems extends Migration
{
    public function up()
    {
        $fields = [
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'status',
            ],
        ];

        $this->forge->addColumn('menu_items', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('menu_items', 'image');
    }
}

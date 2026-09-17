<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPendingStatusToOrders extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'    => "ENUM('pending','selesai','void')",
                'default' => 'pending',
            ],
            'amount_paid' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'change_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
        ];

        $this->forge->modifyColumn('orders', $fields);
    }

    public function down()
    {
        $fields = [
            'status' => [
                'type'    => "ENUM('selesai','void')",
                'default' => 'selesai',
            ],
        ];

        $this->forge->modifyColumn('orders', $fields);
    }
}

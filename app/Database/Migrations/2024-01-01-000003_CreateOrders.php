<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrders extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'invoice_no' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
                'unique' => true,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'discount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0,
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'payment_method' => [
                'type' => "ENUM('tunai')",
                'default' => 'tunai',
            ],
            'amount_paid' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'change_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'status' => [
                'type' => "ENUM('selesai','void')",
                'default' => 'selesai',
            ],
            'void_reason' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
?>

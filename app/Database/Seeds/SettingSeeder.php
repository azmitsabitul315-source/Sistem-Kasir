<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['key' => 'nama_warung', 'value' => 'Warung Ayam Bakar'],
            ['key' => 'alamat',       'value' => 'Jl. Contoh No.123, Kota'],
            ['key' => 'no_telp',      'value' => '0812-3456-7890'],
            ['key' => 'info_nota',    'value' => 'Terima kasih atas kunjungan Anda'],
        ];
        $this->db->table('settings')->insertBatch($settings);
    }
}
?>

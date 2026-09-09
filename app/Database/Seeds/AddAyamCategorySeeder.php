<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddAyamCategorySeeder extends Seeder
{
    public function run()
    {
        // Cek apakah kategori 'Ayam' sudah ada
        $cat = $this->db->table('categories')->where('name', 'Ayam')->get()->getRowArray();
        if (!$cat) {
            $this->db->table('categories')->insert([
                'name'       => 'Ayam',
                'sort_order' => 2, // diletakkan setelah Paket Ayam
            ]);
            $catId = $this->db->insertID();
        } else {
            $catId = $cat['id'];
        }

        // Geser sort order kategori lain jika perlu
        $this->db->query("UPDATE categories SET sort_order = 3 WHERE name = 'Tambahan'");
        $this->db->query("UPDATE categories SET sort_order = 4 WHERE name = 'Minuman'");

        // Daftar menu Ayam (tanpa paket, harga Rp 15.000)
        $ayamItems = [
            ['name' => 'Ayam Bakar (Ala Carte)',           'price' => 15000],
            ['name' => 'Ayam Panggang Santan (Ala Carte)', 'price' => 15000],
            ['name' => 'Ayam Geprek (Ala Carte)',          'price' => 15000],
        ];

        foreach ($ayamItems as $item) {
            $exists = $this->db->table('menu_items')
                ->where('category_id', $catId)
                ->where('name', $item['name'])
                ->get()->getRowArray();

            if (!$exists) {
                $this->db->table('menu_items')->insert([
                    'category_id' => $catId,
                    'name'        => $item['name'],
                    'price'       => $item['price'],
                    'status'      => 'aktif',
                ]);
            }
        }
    }
}

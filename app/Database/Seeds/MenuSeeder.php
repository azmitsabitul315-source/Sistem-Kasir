<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Kategori
        $categories = [
            ['name' => 'Paket Ayam', 'sort_order' => 1],
            ['name' => 'Ayam',       'sort_order' => 2],
            ['name' => 'Tambahan',   'sort_order' => 3],
            ['name' => 'Minuman',    'sort_order' => 4],
        ];
        $this->db->table('categories')->insertBatch($categories);

        // Ambil ID kategori yang baru saja dimasukkan
        $categoryIds = $this->db->table('categories')->select('id, name')->get()->getResultArray();
        $catMap = [];
        foreach ($categoryIds as $c) {
            $catMap[$c['name']] = $c['id'];
        }

        // Menu items (seed awal)
        $menuItems = [
            // Paket Ayam (harga tetap 20000, termasuk nasi & es teh)
            ['category_id' => $catMap['Paket Ayam'], 'name' => 'Paket Ayam Bakar',           'price' => 20000, 'status' => 'aktif'],
            ['category_id' => $catMap['Paket Ayam'], 'name' => 'Paket Ayam Panggang Santan','price' => 20000, 'status' => 'aktif'],
            ['category_id' => $catMap['Paket Ayam'], 'name' => 'Paket Ayam Geprek',          'price' => 20000, 'status' => 'aktif'],

            // Ayam (tanpa paket, harga Rp 15.000)
            ['category_id' => $catMap['Ayam'], 'name' => 'Ayam Bakar (Ala Carte)',           'price' => 15000, 'status' => 'aktif'],
            ['category_id' => $catMap['Ayam'], 'name' => 'Ayam Panggang Santan (Ala Carte)', 'price' => 15000, 'status' => 'aktif'],
            ['category_id' => $catMap['Ayam'], 'name' => 'Ayam Geprek (Ala Carte)',          'price' => 15000, 'status' => 'aktif'],

            // Tambahan
            ['category_id' => $catMap['Tambahan'], 'name' => 'Tempe Bakar',  'price' => 5000,  'status' => 'aktif'],
            ['category_id' => $catMap['Tambahan'], 'name' => 'Tahu Bakar',   'price' => 5000,  'status' => 'aktif'],
            ['category_id' => $catMap['Tambahan'], 'name' => 'Tambah Sambal','price' => 3000,  'status' => 'aktif'],
            ['category_id' => $catMap['Tambahan'], 'name' => 'Nasi Putih',   'price' => 3000,  'status' => 'aktif'],

            // Minuman
            ['category_id' => $catMap['Minuman'], 'name' => 'Es Teh',   'price' => 5000, 'status' => 'aktif'],
            ['category_id' => $catMap['Minuman'], 'name' => 'Es Jeruk', 'price' => 5000, 'status' => 'aktif'],
            ['category_id' => $catMap['Minuman'], 'name' => 'Teh Anget','price' => 5000, 'status' => 'aktif'],
        ];
        $this->db->table('menu_items')->insertBatch($menuItems);
    }
}
?>

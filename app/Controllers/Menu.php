<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\MenuItemModel;

class Menu extends BaseController
{
    protected $categoryModel;
    protected $menuItemModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->menuItemModel = new MenuItemModel();
    }

    /**
     * Daftar semua menu
     */
    public function index()
    {
        $menuItems  = $this->menuItemModel->orderBy('category_id')->findAll();
        $categories = $this->categoryModel->orderBy('sort_order')->findAll();

        // Buat map kategori
        $catMap = [];
        foreach ($categories as $c) {
            $catMap[$c['id']] = $c['name'];
        }

        $data = [
            'title'      => 'Kelola Menu',
            'menuItems'  => $menuItems,
            'categories' => $categories,
            'catMap'     => $catMap,
        ];

        return view('menu/index', $data);
    }

    /**
     * Form tambah menu baru
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Menu',
            'categories' => $this->categoryModel->orderBy('sort_order')->findAll(),
            'menu'       => null,
        ];
        return view('menu/form', $data);
    }

    /**
     * Simpan menu baru
     */
    /**
     * Simpan menu baru
     */
    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]',
            'category_id' => 'required|integer',
            'price'       => 'required|numeric|greater_than[0]',
            'status'      => 'required|in_list[aktif,habis,nonaktif]',
            'image'       => 'is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imageName = null;
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $imageName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/menu', $imageName);
        }

        $this->menuItemModel->insert([
            'name'        => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category_id'),
            'price'       => $this->request->getPost('price'),
            'status'      => $this->request->getPost('status'),
            'image'       => $imageName,
        ]);

        return redirect()->to('/menu')->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Form edit menu
     */
    public function edit($id)
    {
        $menu = $this->menuItemModel->find($id);
        if (!$menu) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title'      => 'Edit Menu',
            'categories' => $this->categoryModel->orderBy('sort_order')->findAll(),
            'menu'       => $menu,
        ];
        return view('menu/form', $data);
    }

    /**
     * Update menu
     */
    public function update($id)
    {
        $menu = $this->menuItemModel->find($id);
        if (!$menu) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]',
            'category_id' => 'required|integer',
            'price'       => 'required|numeric|greater_than[0]',
            'status'      => 'required|in_list[aktif,habis,nonaktif]',
            'image'       => 'is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imageName = $menu['image'];
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Hapus foto lama jika ada
            if ($imageName && file_exists(FCPATH . 'uploads/menu/' . $imageName)) {
                @unlink(FCPATH . 'uploads/menu/' . $imageName);
            }
            $imageName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/menu', $imageName);
        }

        $this->menuItemModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category_id'),
            'price'       => $this->request->getPost('price'),
            'status'      => $this->request->getPost('status'),
            'image'       => $imageName,
        ]);

        return redirect()->to('/menu')->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Toggle status menu (aktif/habis/nonaktif)
     */
    public function toggleStatus($id)
    {
        $menu = $this->menuItemModel->find($id);
        if (!$menu) {
            return $this->response->setJSON(['success' => false, 'message' => 'Menu tidak ditemukan.']);
        }

        $newStatus = $this->request->getPost('status');
        if (!in_array($newStatus, ['aktif', 'habis', 'nonaktif'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status tidak valid.']);
        }

        $this->menuItemModel->update($id, ['status' => $newStatus]);

        return $this->response->setJSON(['success' => true, 'message' => 'Status diubah menjadi ' . $newStatus]);
    }
}

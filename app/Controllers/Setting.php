<?php

namespace App\Controllers;

use App\Models\SettingModel;

class Setting extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    /**
     * Halaman pengaturan
     */
    public function index()
    {
        $data = [
            'title'    => 'Pengaturan',
            'settings' => $this->settingModel->getAllSettings(),
        ];

        return view('setting/index', $data);
    }

    /**
     * Update pengaturan
     */
    public function update()
    {
        $keys = ['nama_warung', 'alamat', 'no_telp', 'info_nota'];

        foreach ($keys as $key) {
            $value = $this->request->getPost($key) ?? '';
            $this->settingModel->setValue($key, $value);
        }

        return redirect()->to('/setting')->with('success', 'Pengaturan berhasil disimpan.');
    }
}

<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\SettingModel;

class Nota extends BaseController
{
    /**
     * Cetak nota — halaman khusus print tanpa sidebar
     */
    public function cetak($id)
    {
        $orderModel     = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $settingModel   = new SettingModel();

        $order = $orderModel->find($id);
        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $items    = $orderItemModel->where('order_id', $id)->findAll();
        $settings = $settingModel->getAllSettings();

        $data = [
            'title'    => 'Nota ' . $order['invoice_no'],
            'order'    => $order,
            'items'    => $items,
            'settings' => $settings,
        ];

        return view('nota/cetak', $data);
    }
}

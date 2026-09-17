<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\SettingModel;

class Pending extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $settingModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->settingModel   = new SettingModel();
    }

    /**
     * Halaman khusus untuk menampilkan daftar Pesanan Pending
     */
    public function index()
    {
        $orders = $this->orderModel
            ->where('status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        foreach ($orders as &$order) {
            $order['items'] = $this->orderItemModel->where('order_id', $order['id'])->findAll();
        }

        $data = [
            'title'    => 'Pesanan Pending (Bill Aktif)',
            'orders'   => $orders,
            'settings' => $this->settingModel->getAllSettings(),
        ];

        return view('pending/index', $data);
    }
}

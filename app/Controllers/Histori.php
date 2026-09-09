<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;

class Histori extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    /**
     * Daftar semua transaksi
     */
    public function index()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $orders = $this->orderModel
            ->where('DATE(created_at)', $tanggal)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title'   => 'Histori Pesanan',
            'orders'  => $orders,
            'tanggal' => $tanggal,
        ];

        return view('histori/index', $data);
    }

    /**
     * Detail satu transaksi
     */
    public function detail($id)
    {
        $order = $this->orderModel->find($id);
        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $items = $this->orderItemModel->where('order_id', $id)->findAll();

        $data = [
            'title' => 'Detail Transaksi ' . $order['invoice_no'],
            'order' => $order,
            'items' => $items,
        ];

        return view('histori/detail', $data);
    }

    /**
     * Void transaksi
     */
    public function void($id)
    {
        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak ditemukan.']);
        }

        if ($order['status'] === 'void') {
            return $this->response->setJSON(['success' => false, 'message' => 'Transaksi sudah di-void.']);
        }

        $reason = $this->request->getPost('void_reason');
        if (empty(trim($reason))) {
            return $this->response->setJSON(['success' => false, 'message' => 'Alasan void wajib diisi.']);
        }

        $this->orderModel->update($id, [
            'status'      => 'void',
            'void_reason' => trim($reason),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil di-void.']);
    }
}

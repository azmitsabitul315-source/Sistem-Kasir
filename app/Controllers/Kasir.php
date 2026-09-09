<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\MenuItemModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\SettingModel;

class Kasir extends BaseController
{
    protected $categoryModel;
    protected $menuItemModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $settingModel;

    public function __construct()
    {
        $this->categoryModel  = new CategoryModel();
        $this->menuItemModel  = new MenuItemModel();
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->settingModel   = new SettingModel();
    }

    /**
     * Halaman utama kasir — menampilkan menu + keranjang
     */
    public function index()
    {
        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')->findAll();
        $menuItems  = $this->menuItemModel->where('status', 'aktif')->findAll();

        // Kelompokkan menu berdasarkan kategori
        $grouped = [];
        foreach ($categories as $cat) {
            $grouped[$cat['id']] = [
                'category' => $cat,
                'items'    => [],
            ];
        }
        foreach ($menuItems as $item) {
            if (isset($grouped[$item['category_id']])) {
                $grouped[$item['category_id']]['items'][] = $item;
            }
        }

        $data = [
            'title'      => 'Kasir',
            'categories' => $grouped,
            'settings'   => $this->settingModel->getAllSettings(),
        ];

        return view('kasir/index', $data);
    }

    /**
     * Proses pembayaran — menerima POST dari form kasir
     */
    public function proses()
    {
        $request = $this->request;

        // Ambil data keranjang dari POST
        $items      = $request->getPost('items');      // array of {menu_item_id, qty, note}
        $amountPaid = $request->getPost('amount_paid'); // uang bayar

        // Validasi: keranjang tidak boleh kosong
        if (empty($items) || !is_array($items)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Keranjang kosong.',
            ]);
        }

        // Validasi dan hitung total server-side
        $orderItems = [];
        $subtotal   = 0;

        foreach ($items as $item) {
            $menuId = (int) ($item['menu_item_id'] ?? 0);
            $qty    = (int) ($item['qty'] ?? 0);
            $note   = trim($item['note'] ?? '');

            // Qty harus minimal 1
            if ($qty < 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Qty harus minimal 1.',
                ]);
            }

            // Cari menu dari database
            $menu = $this->menuItemModel->find($menuId);
            if (!$menu) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Menu tidak ditemukan: ID ' . $menuId,
                ]);
            }
            if ($menu['status'] !== 'aktif') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Menu "' . $menu['name'] . '" tidak tersedia.',
                ]);
            }

            $price        = (float) $menu['price'];
            $itemSubtotal = $price * $qty;
            $subtotal    += $itemSubtotal;

            $orderItems[] = [
                'menu_item_id' => $menuId,
                'item_name'    => $menu['name'],       // snapshot
                'price'        => $price,              // snapshot
                'qty'          => $qty,
                'note'         => $note,
                'item_subtotal'=> $itemSubtotal,
            ];
        }

        $discount = 0;
        $total    = max(0, $subtotal - $discount);

        // Validasi pembayaran
        $amountPaid = (float) $amountPaid;
        if ($amountPaid < $total) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Uang bayar kurang. Total: Rp' . number_format($total, 0, ',', '.'),
            ]);
        }

        $changeAmount = $amountPaid - $total;

        // Generate nomor nota: INV-YYYYMMDD-XXXX
        $today      = date('Ymd');
        $lastOrder  = $this->orderModel
            ->like('invoice_no', 'INV-' . $today, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $sequence = 1;
        if ($lastOrder) {
            $parts    = explode('-', $lastOrder['invoice_no']);
            $sequence = (int) end($parts) + 1;
        }
        $invoiceNo = 'INV-' . $today . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Simpan dengan DB transaction
        $db = \Config\Database::connect();
        $db->transStart();

        $orderId = $this->orderModel->insert([
            'invoice_no'     => $invoiceNo,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'total'          => $total,
            'payment_method' => 'tunai',
            'amount_paid'    => $amountPaid,
            'change_amount'  => $changeAmount,
            'status'         => 'selesai',
        ]);

        foreach ($orderItems as &$oi) {
            $oi['order_id'] = $orderId;
        }
        $this->orderItemModel->insertBatch($orderItems);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi.',
            ]);
        }

        return $this->response->setJSON([
            'success'    => true,
            'message'    => 'Transaksi berhasil!',
            'invoice_no' => $invoiceNo,
            'total'      => $total,
            'amount_paid'=> $amountPaid,
            'change'     => $changeAmount,
            'order_id'   => $orderId,
        ]);
    }
}

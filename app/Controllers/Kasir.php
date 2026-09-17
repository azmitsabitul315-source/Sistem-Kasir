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
     * Helper untuk memvalidasi dan menghitung order items dari data POST
     */
    private function validateAndCalculateItems(array $items)
    {
        $orderItems = [];
        $subtotal   = 0;

        foreach ($items as $item) {
            $menuId = (int) ($item['menu_item_id'] ?? 0);
            $qty    = (int) ($item['qty'] ?? 0);
            $note   = trim($item['note'] ?? '');

            if ($qty < 1) {
                return ['error' => 'Qty harus minimal 1.'];
            }

            $menu = $this->menuItemModel->find($menuId);
            if (!$menu) {
                return ['error' => 'Menu tidak ditemukan: ID ' . $menuId];
            }
            if ($menu['status'] !== 'aktif') {
                return ['error' => 'Menu "' . $menu['name'] . '" tidak tersedia.'];
            }

            $price        = (float) $menu['price'];
            $itemSubtotal = $price * $qty;
            $subtotal    += $itemSubtotal;

            $orderItems[] = [
                'menu_item_id' => $menuId,
                'item_name'    => $menu['name'],
                'price'        => $price,
                'qty'          => $qty,
                'note'         => $note,
                'item_subtotal'=> $itemSubtotal,
            ];
        }

        return [
            'orderItems' => $orderItems,
            'subtotal'   => $subtotal,
        ];
    }

    /**
     * Generate Nomor Nota: INV-YYYYMMDD-XXXX
     */
    private function generateInvoiceNo()
    {
        $today     = date('Ymd');
        $lastOrder = $this->orderModel
            ->like('invoice_no', 'INV-' . $today, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $sequence = 1;
        if ($lastOrder) {
            $parts    = explode('-', $lastOrder['invoice_no']);
            $sequence = (int) end($parts) + 1;
        }

        return 'INV-' . $today . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Simpan Pesanan (Draft Bill / Pending Status)
     * Pesan Dulu -> Simpan ke Antrian Bill Aktif
     */
    public function simpanPesanan()
    {
        $request = $this->request;
        $orderId = (int) $request->getPost('order_id');
        $items   = $request->getPost('items');

        if (empty($items) || !is_array($items)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Keranjang pesanan kosong.',
            ]);
        }

        $calculated = $this->validateAndCalculateItems($items);
        if (isset($calculated['error'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $calculated['error'],
            ]);
        }

        $orderItems = $calculated['orderItems'];
        $subtotal   = $calculated['subtotal'];
        $discount   = 0;
        $total      = max(0, $subtotal - $discount);

        $db = \Config\Database::connect();
        $db->transStart();

        if ($orderId > 0) {
            // Update pesanan pending yang sudah ada
            $existing = $this->orderModel->find($orderId);
            if (!$existing || $existing['status'] !== 'pending') {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan atau sudah selesai.',
                ]);
            }

            $invoiceNo = $existing['invoice_no'];
            $this->orderModel->update($orderId, [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total'    => $total,
            ]);

            // Hapus items lama, ganti dengan baru
            $this->orderItemModel->where('order_id', $orderId)->delete();
        } else {
            // Buat pesanan pending baru
            $invoiceNo = $this->generateInvoiceNo();
            $orderId   = $this->orderModel->insert([
                'invoice_no'     => $invoiceNo,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => 'tunai',
                'amount_paid'    => 0,
                'change_amount'  => 0,
                'status'         => 'pending',
            ]);
        }

        foreach ($orderItems as &$oi) {
            $oi['order_id'] = $orderId;
        }
        $this->orderItemModel->insertBatch($orderItems);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan pesanan.',
            ]);
        }

        return $this->response->setJSON([
            'success'    => true,
            'message'    => 'Pesanan berhasil disimpan ke Bill Aktif!',
            'order_id'   => $orderId,
            'invoice_no' => $invoiceNo,
            'total'      => $total,
        ]);
    }

    /**
     * Ambil semua daftar pesanan pending (Bill Aktif)
     */
    public function getPendingOrders()
    {
        $orders = $this->orderModel
            ->where('status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        foreach ($orders as &$order) {
            $items = $this->orderItemModel->where('order_id', $order['id'])->findAll();
            $order['items']      = $items;
            $order['item_count'] = count($items);
        }

        return $this->response->setJSON([
            'success' => true,
            'orders'  => $orders,
        ]);
    }

    /**
     * Ambil detail 1 pesanan (untuk edit / preview)
     */
    public function getOrderDetail($id)
    {
        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ]);
        }

        $items = $this->orderItemModel->where('order_id', $id)->findAll();

        return $this->response->setJSON([
            'success' => true,
            'order'   => $order,
            'items'   => $items,
        ]);
    }

    /**
     * Batalkan pesanan pending
     */
    public function batalPesanan($id)
    {
        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ]);
        }

        if ($order['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya pesanan pending yang dapat dibatalkan dari antrian.',
            ]);
        }

        $this->orderModel->update($id, [
            'status'      => 'void',
            'void_reason' => 'Dibatalkan dari antrian pesanan.',
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pesanan ' . $order['invoice_no'] . ' berhasil dibatalkan.',
        ]);
    }

    /**
     * Proses pembayaran (Langsung atau Bayar Bill Pending)
     */
    public function proses()
    {
        $request    = $this->request;
        $orderId    = (int) $request->getPost('order_id');
        $items      = $request->getPost('items');
        $amountPaid = (float) $request->getPost('amount_paid');

        // Skenario 1: Bayar pesanan pending yang sudah ada
        if ($orderId > 0) {
            $existing = $this->orderModel->find($orderId);
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ]);
            }
            if ($existing['status'] !== 'pending') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pesanan sudah lunas / di-void.',
                ]);
            }

            // Jika ada perubahan items saat bayar, update items dulu
            $db = \Config\Database::connect();
            $db->transStart();

            if (!empty($items) && is_array($items)) {
                $calculated = $this->validateAndCalculateItems($items);
                if (isset($calculated['error'])) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $calculated['error'],
                    ]);
                }

                $orderItems = $calculated['orderItems'];
                $subtotal   = $calculated['subtotal'];
                $discount   = 0;
                $total      = max(0, $subtotal - $discount);

                $this->orderItemModel->where('order_id', $orderId)->delete();
                foreach ($orderItems as &$oi) {
                    $oi['order_id'] = $orderId;
                }
                $this->orderItemModel->insertBatch($orderItems);
            } else {
                $total = (float) $existing['total'];
            }

            if ($amountPaid < $total) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Uang bayar kurang. Total: Rp' . number_format($total, 0, ',', '.'),
                ]);
            }

            $changeAmount = $amountPaid - $total;

            $this->orderModel->update($orderId, [
                'subtotal'       => isset($subtotal) ? $subtotal : $existing['subtotal'],
                'total'          => $total,
                'amount_paid'    => $amountPaid,
                'change_amount'  => $changeAmount,
                'payment_method' => 'tunai',
                'status'         => 'selesai',
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses pembayaran.',
                ]);
            }

            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'Pembayaran berhasil!',
                'invoice_no' => $existing['invoice_no'],
                'total'      => $total,
                'amount_paid'=> $amountPaid,
                'change'     => $changeAmount,
                'order_id'   => $orderId,
            ]);
        }

        // Skenario 2: Bayar langsung (langsung checkout baru)
        if (empty($items) || !is_array($items)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Keranjang kosong.',
            ]);
        }

        $calculated = $this->validateAndCalculateItems($items);
        if (isset($calculated['error'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $calculated['error'],
            ]);
        }

        $orderItems = $calculated['orderItems'];
        $subtotal   = $calculated['subtotal'];
        $discount   = 0;
        $total      = max(0, $subtotal - $discount);

        if ($amountPaid < $total) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Uang bayar kurang. Total: Rp' . number_format($total, 0, ',', '.'),
            ]);
        }

        $changeAmount = $amountPaid - $total;
        $invoiceNo    = $this->generateInvoiceNo();

        $db = \Config\Database::connect();
        $db->transStart();

        $newOrderId = $this->orderModel->insert([
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
            $oi['order_id'] = $newOrderId;
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
            'order_id'   => $newOrderId,
        ]);
    }
}

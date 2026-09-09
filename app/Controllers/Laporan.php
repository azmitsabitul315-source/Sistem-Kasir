<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;

class Laporan extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    /**
     * Laporan harian
     */
    public function harian()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $db = \Config\Database::connect();

        // Omzet & jumlah transaksi hari ini
        $summary = $db->query("
            SELECT
                COUNT(*) AS jumlah_transaksi,
                COALESCE(SUM(total), 0) AS total_omzet
            FROM orders
            WHERE status = 'selesai'
              AND DATE(created_at) = ?
        ", [$tanggal])->getRowArray();

        // Rata-rata transaksi
        $avgTransaksi = $summary['jumlah_transaksi'] > 0
            ? $summary['total_omzet'] / $summary['jumlah_transaksi']
            : 0;

        // Menu terlaris hari ini
        $menuTerlaris = $db->query("
            SELECT
                oi.item_name,
                SUM(oi.qty) AS total_terjual,
                SUM(oi.item_subtotal) AS omzet_menu
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE o.status = 'selesai'
              AND DATE(o.created_at) = ?
            GROUP BY oi.item_name
            ORDER BY total_terjual DESC
            LIMIT 10
        ", [$tanggal])->getResultArray();

        $data = [
            'title'         => 'Laporan Harian',
            'tanggal'       => $tanggal,
            'summary'       => $summary,
            'avgTransaksi'  => $avgTransaksi,
            'menuTerlaris'  => $menuTerlaris,
        ];

        return view('laporan/harian', $data);
    }

    /**
     * Laporan bulanan
     */
    public function bulanan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $db = \Config\Database::connect();

        // Omzet bulanan total
        $summaryBulan = $db->query("
            SELECT
                COUNT(*) AS jumlah_transaksi,
                COALESCE(SUM(total), 0) AS total_omzet
            FROM orders
            WHERE status = 'selesai'
              AND YEAR(created_at) = ?
              AND MONTH(created_at) = ?
        ", [$tahun, $bulan])->getRowArray();

        // Tren harian
        $trenHarian = $db->query("
            SELECT
                DATE(created_at) AS tanggal,
                COUNT(*) AS jumlah_transaksi,
                SUM(total) AS omzet_harian
            FROM orders
            WHERE status = 'selesai'
              AND YEAR(created_at) = ?
              AND MONTH(created_at) = ?
            GROUP BY DATE(created_at)
            ORDER BY tanggal
        ", [$tahun, $bulan])->getResultArray();

        // Menu terlaris bulan ini
        $menuTerlaris = $db->query("
            SELECT
                oi.item_name,
                SUM(oi.qty) AS total_terjual,
                SUM(oi.item_subtotal) AS omzet_menu
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE o.status = 'selesai'
              AND YEAR(o.created_at) = ?
              AND MONTH(o.created_at) = ?
            GROUP BY oi.item_name
            ORDER BY total_terjual DESC
            LIMIT 10
        ", [$tahun, $bulan])->getResultArray();

        // Perbandingan bulan sebelumnya
        $prevMonth = (int) $bulan - 1;
        $prevYear  = (int) $tahun;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $prevSummary = $db->query("
            SELECT COALESCE(SUM(total), 0) AS total_omzet
            FROM orders
            WHERE status = 'selesai'
              AND YEAR(created_at) = ?
              AND MONTH(created_at) = ?
        ", [$prevYear, $prevMonth])->getRowArray();

        $data = [
            'title'        => 'Laporan Bulanan',
            'bulan'        => $bulan,
            'tahun'        => $tahun,
            'summaryBulan' => $summaryBulan,
            'trenHarian'   => $trenHarian,
            'menuTerlaris' => $menuTerlaris,
            'prevOmzet'    => $prevSummary['total_omzet'],
        ];

        return view('laporan/bulanan', $data);
    }
}

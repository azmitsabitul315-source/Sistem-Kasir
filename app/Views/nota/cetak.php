<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota <?= esc($order['invoice_no']) ?></title>
    <link rel="stylesheet" href="<?= base_url('css/print.css') ?>">
    <style>
        /* Screen style untuk preview nota */
        @media screen {
            body {
                font-family: 'Courier New', monospace;
                font-size: 12px;
                background: #f5f5f5;
                padding: 20px;
                color: #000;
            }
            .nota-container {
                max-width: 320px;
                margin: 0 auto;
                background: #fff;
                padding: 20px;
                border: 1px dashed #ccc;
                box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            }
            .nota-header { text-align: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px dashed #333; }
            .nota-header h2 { font-size: 16px; margin-bottom: 4px; }
            .nota-header p { margin: 2px 0; font-size: 11px; }
            .nota-info { margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dashed #333; font-size: 11px; }
            .nota-info td { padding: 2px 0; font-size: 11px; }
            .nota-items td { padding: 3px 0; font-size: 11px; }
            .nota-items .item-name { font-weight: bold; }
            .nota-items .item-detail { padding-left: 10px; }
            .nota-totals { border-top: 1px dashed #333; padding-top: 8px; margin-top: 4px; }
            .nota-totals td { padding: 2px 0; font-size: 11px; }
            .nota-totals .grand-total td { font-size: 14px; font-weight: bold; padding-top: 6px; }
            .nota-footer { text-align: center; border-top: 1px dashed #333; padding-top: 8px; margin-top: 8px; font-size: 11px; }
            .nota-footer p { margin: 2px 0; }
            .btn-print {
                display: block;
                max-width: 320px;
                margin: 16px auto 0;
                padding: 12px;
                background: linear-gradient(135deg, #C0392B, #E67E22);
                color: #fff;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                font-family: 'Poppins', sans-serif;
            }
            .btn-print:hover { opacity: 0.9; }
        }
    </style>
</head>
<body>

<div class="nota-container">
    <!-- Header Nota -->
    <div class="nota-header">
        <h2><?= esc($settings['nama_warung'] ?? 'Warung Ayam Bakar') ?></h2>
        <p><?= esc($settings['alamat'] ?? '') ?></p>
        <?php if (!empty($settings['no_telp'])): ?>
            <p>Telp: <?= esc($settings['no_telp']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Info Nota -->
    <div class="nota-info">
        <table style="width:100%;">
            <tr>
                <td>No. Nota</td>
                <td style="text-align:right;"><?= esc($order['invoice_no']) ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td style="text-align:right;"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
            </tr>
        </table>
    </div>

    <!-- Daftar Item -->
    <table class="nota-items" style="width:100%;">
        <?php foreach ($items as $item): ?>
            <tr>
                <td colspan="3" class="item-name"><?= esc($item['item_name']) ?></td>
            </tr>
            <tr>
                <td class="item-detail"><?= $item['qty'] ?> x Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                <td></td>
                <td style="text-align:right;">Rp<?= number_format($item['item_subtotal'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Totals -->
    <div class="nota-totals">
        <table style="width:100%;">
            <tr>
                <td>Subtotal</td>
                <td style="text-align:right;">Rp<?= number_format($order['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php if ($order['discount'] > 0): ?>
            <tr>
                <td>Diskon</td>
                <td style="text-align:right;">-Rp<?= number_format($order['discount'], 0, ',', '.') ?></td>
            </tr>
            <?php endif; ?>
            <tr class="grand-total">
                <td>TOTAL</td>
                <td style="text-align:right;">Rp<?= number_format($order['total'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td style="text-align:right;">Rp<?= number_format($order['amount_paid'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td style="text-align:right;">Rp<?= number_format($order['change_amount'], 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="nota-footer">
        <p><?= esc($settings['info_nota'] ?? 'Terima kasih atas kunjungan Anda') ?></p>
        <?php if ($order['status'] === 'pending'): ?>
            <p style="font-weight:bold;color:#e67e22;border:1px dashed #e67e22;padding:4px;margin-top:6px;">*** STRUK SEMENTARA / DAPUR ***<br><span style="font-size:10px;">(BELUM LUNAS)</span></p>
        <?php elseif ($order['status'] === 'void'): ?>
            <p style="font-weight:bold;color:red;">*** VOID ***</p>
        <?php endif; ?>
    </div>
</div>

<button class="btn-print no-print" onclick="window.print()">🖨️ Cetak Nota</button>

<script>
    // Otomatis buka dialog cetak saat halaman nota selesai dimuat
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
        }, 300);
    });
</script>

</body>
</html>

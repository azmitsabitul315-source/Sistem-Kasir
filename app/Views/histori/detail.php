<?= $this->include('layout/header') ?>

<div style="margin-bottom:16px;">
    <a href="<?= base_url('histori') ?>" class="btn btn-secondary" style="padding:6px 14px;font-size:0.82rem;">← Kembali ke Histori</a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <span><?= esc($order['invoice_no']) ?></span>
        <span>
            <?php if ($order['status'] === 'selesai'): ?>
                <span class="badge badge-success">Selesai</span>
            <?php else: ?>
                <span class="badge badge-danger">Void</span>
            <?php endif; ?>
        </span>
    </div>
    <div class="card-body">
        <table style="width:auto;margin-bottom:16px;">
            <tr>
                <td style="padding:4px 16px 4px 0;font-weight:600;color:var(--text-muted);font-size:0.85rem;">Waktu</td>
                <td style="padding:4px 0;font-size:0.85rem;"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td>
            </tr>
            <?php if ($order['status'] === 'void' && $order['void_reason']): ?>
            <tr>
                <td style="padding:4px 16px 4px 0;font-weight:600;color:var(--text-muted);font-size:0.85rem;">Alasan Void</td>
                <td style="padding:4px 0;font-size:0.85rem;color:var(--primary);"><?= esc($order['void_reason']) ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th class="text-right">Harga</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?= esc($item['item_name']) ?>
                                <?php if ($item['note']): ?>
                                    <br><small class="text-muted"><?= esc($item['note']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= $item['qty'] ?></td>
                            <td class="text-right">Rp<?= number_format($item['item_subtotal'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="border-top:2px solid var(--border);padding-top:12px;margin-top:12px;">
            <div class="payment-row"><span>Subtotal</span><span>Rp<?= number_format($order['subtotal'], 0, ',', '.') ?></span></div>
            <?php if ($order['discount'] > 0): ?>
                <div class="payment-row"><span>Diskon</span><span>-Rp<?= number_format($order['discount'], 0, ',', '.') ?></span></div>
            <?php endif; ?>
            <div class="payment-row total"><span>Total</span><span>Rp<?= number_format($order['total'], 0, ',', '.') ?></span></div>
            <div class="payment-row"><span>Bayar</span><span>Rp<?= number_format($order['amount_paid'], 0, ',', '.') ?></span></div>
            <div class="payment-row"><span>Kembalian</span><span class="text-success" style="font-weight:700;">Rp<?= number_format($order['change_amount'], 0, ',', '.') ?></span></div>
        </div>

        <div style="margin-top:16px;">
            <a href="<?= base_url('nota/cetak/' . $order['id']) ?>" target="_blank" class="btn btn-primary">🖨️ Cetak Nota</a>
        </div>
    </div>
</div>

<?= $this->include('layout/footer') ?>

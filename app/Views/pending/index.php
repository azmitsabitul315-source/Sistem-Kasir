<?= $this->include('layout/header') ?>

<div class="d-flex justify-content-between align-items-center mb-3" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <div>
        <h2 style="font-size:1.4rem;font-weight:700;color:var(--text-dark);">⏳ Pesanan Pending (Bill Aktif)</h2>
        <p style="font-size:0.85rem;color:var(--text-muted);margin-top:2px;">Daftar pesanan berjalan yang belum dilakukan pembayaran/checkout.</p>
    </div>
    <a href="<?= base_url('kasir') ?>" class="btn btn-primary" style="padding:10px 18px;font-weight:600;display:flex;align-items:center;gap:6px;">
        ➕ Buat Pesanan Baru
    </a>
</div>

<?php if (empty($orders)): ?>
    <div class="card p-4 text-center text-muted" style="padding:40px;text-align:center;background:#fff;border-radius:12px;box-shadow:var(--shadow);">
        <p style="font-size:2.5rem;margin-bottom:8px;">☕</p>
        <p style="font-size:1.1rem;font-weight:600;color:var(--text-dark);">Tidak ada pesanan pending saat ini.</p>
        <p style="font-size:0.85rem;color:var(--text-muted);">Semua pesanan telah lunas atau belum ada pesanan baru.</p>
    </div>
<?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:16px;">
        <?php foreach ($orders as $order): ?>
            <div class="card" style="padding:16px;background:#fff;border-radius:12px;border:1px solid var(--border);box-shadow:var(--shadow);display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:10px;border-bottom:1px dashed #e0d6cc;margin-bottom:10px;">
                        <div>
                            <strong style="font-size:1.05rem;color:var(--primary);"><?= esc($order['invoice_no']) ?></strong>
                            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">
                                🕒 <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                            </div>
                        </div>
                        <span class="badge" style="background:#f39c12;color:#fff;padding:4px 10px;border-radius:6px;font-size:0.8rem;font-weight:600;">Pending</span>
                    </div>

                    <div style="margin-bottom:12px;">
                        <div style="font-size:0.8rem;font-weight:600;color:var(--text-muted);margin-bottom:6px;">Rincian Pesanan:</div>
                        <ul style="padding-left:18px;margin:0;font-size:0.88rem;color:var(--text-dark);">
                            <?php foreach ($order['items'] as $item): ?>
                                <li style="margin-bottom:3px;">
                                    <strong><?= esc($item['item_name']) ?></strong> x<?= $item['qty'] ?> 
                                    <span style="color:var(--text-muted);font-size:0.8rem;">(Rp<?= number_format($item['item_subtotal'], 0, ',', '.') ?>)</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding-top:10px;border-top:1px solid #f0e6dc;margin-bottom:12px;">
                        <span style="font-size:0.85rem;color:var(--text-muted);">Total Tagihan:</span>
                        <span style="font-size:1.25rem;font-weight:700;color:var(--primary);">Rp<?= number_format($order['total'], 0, ',', '.') ?></span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <button onclick="bayarPendingDirect(<?= $order['id'] ?>, '<?= esc($order['invoice_no']) ?>', <?= $order['total'] ?>)" class="btn btn-primary" style="padding:8px;font-weight:600;font-size:0.85rem;background:#27ae60;">
                            💰 Bayar
                        </button>
                        <a href="<?= base_url('kasir?edit=' . $order['id']) ?>" class="btn btn-secondary" style="padding:8px;font-weight:600;font-size:0.85rem;text-align:center;">
                            ✏️ Edit Menu
                        </a>
                        <a href="<?= base_url('nota/cetak/' . $order['id']) ?>" target="_blank" class="btn btn-secondary" style="padding:8px;font-weight:600;font-size:0.85rem;text-align:center;background:#7f8c8d;color:#fff;">
                            🖨️ Struk Dapur
                        </a>
                        <button onclick="batalPendingDirect(<?= $order['id'] ?>, '<?= esc($order['invoice_no']) ?>')" class="btn btn-danger" style="padding:8px;font-weight:600;font-size:0.85rem;">
                            ❌ Batalkan
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal Pembayaran Langsung dari Halaman Pending -->
<div class="modal-overlay" id="modal-payment-pending">
    <div class="modal">
        <div class="modal-header">
            <h3>💰 Pembayaran Bill Pending</h3>
            <button class="modal-close" onclick="closePendingPayment()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="background:#f8f9fa;padding:10px;border-radius:6px;margin-bottom:12px;font-size:0.9rem;font-weight:600;">
                No. Nota: <span id="pending-pay-invoice" style="color:var(--primary);"></span>
            </div>
            <div class="payment-row total">
                <span>Total Tagihan</span>
                <span id="pending-pay-total">Rp0</span>
            </div>
            <div class="form-group mt-2">
                <label for="pending-amount-paid">Uang Bayar</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:1.2rem;font-weight:700;color:var(--text-muted);">Rp</span>
                    <input type="text" id="pending-amount-paid" class="form-control form-control-lg"
                           style="padding-left:50px;"
                           placeholder="0" inputmode="numeric" oninput="formatUangBayarPending(this); calcChangePending();">
                </div>
            </div>
            <div class="payment-row">
                <span>Kembalian</span>
                <span class="kembalian" id="pending-pay-change">Rp0</span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closePendingPayment()">Batal</button>
            <button class="btn btn-primary btn-lg" id="btn-finish-pending" onclick="finishPendingPayment()" disabled>
                ✅ Selesaikan & Cetak Nota
            </button>
        </div>
    </div>
</div>

<script>
let targetPendingId = null;
let targetPendingTotal = 0;

function formatRibuan(angka) {
    let str = String(Math.round(angka));
    let hasil = '';
    let count = 0;
    for (let i = str.length - 1; i >= 0; i--) {
        hasil = str[i] + hasil;
        count++;
        if (count % 3 === 0 && i > 0) {
            hasil = '.' + hasil;
        }
    }
    return hasil;
}

function parseUang(str) {
    if (!str) return 0;
    let angka = str.replace(/\D/g, '');
    return angka === '' ? 0 : parseInt(angka, 10);
}

function formatUangBayarPending(el) {
    let cursorPos = el.selectionStart;
    let oldLen = el.value.length;
    let raw = el.value.replace(/\D/g, '');
    if (raw === '' || raw === '0') {
        el.value = '';
        return;
    }
    raw = String(parseInt(raw, 10));
    el.value = formatRibuan(raw);
    let newLen = el.value.length;
    let diff = newLen - oldLen;
    let newPos = cursorPos + diff;
    if (newPos < 0) newPos = 0;
    el.setSelectionRange(newPos, newPos);
}

function bayarPendingDirect(id, invoiceNo, total) {
    targetPendingId = id;
    targetPendingTotal = total;
    document.getElementById('pending-pay-invoice').textContent = invoiceNo;
    document.getElementById('pending-pay-total').textContent = 'Rp' + formatRibuan(total);
    document.getElementById('pending-amount-paid').value = '';
    document.getElementById('pending-pay-change').textContent = 'Rp0';
    document.getElementById('pending-pay-change').classList.remove('kurang');
    document.getElementById('btn-finish-pending').disabled = true;

    document.getElementById('modal-payment-pending').classList.add('show');
    setTimeout(() => document.getElementById('pending-amount-paid').focus(), 200);
}

function closePendingPayment() {
    document.getElementById('modal-payment-pending').classList.remove('show');
    targetPendingId = null;
}

function calcChangePending() {
    const paid = parseUang(document.getElementById('pending-amount-paid').value);
    const change = paid - targetPendingTotal;
    const changeEl = document.getElementById('pending-pay-change');
    const btnFinish = document.getElementById('btn-finish-pending');

    if (paid >= targetPendingTotal && paid > 0) {
        changeEl.textContent = 'Rp' + formatRibuan(change);
        changeEl.classList.remove('kurang');
        btnFinish.disabled = false;
    } else {
        changeEl.textContent = paid > 0 ? 'Kurang Rp' + formatRibuan(targetPendingTotal - paid) : 'Rp0';
        changeEl.classList.add('kurang');
        btnFinish.disabled = true;
    }
}

async function finishPendingPayment() {
    const amountPaid = parseUang(document.getElementById('pending-amount-paid').value);
    if (amountPaid < targetPendingTotal) {
        alert('Uang bayar kurang!');
        return;
    }

    const formData = new FormData();
    formData.append('order_id', targetPendingId);
    formData.append('amount_paid', amountPaid);

    const resp = await fetch('<?= base_url('kasir/proses') ?>', {
        method: 'POST',
        body: formData,
    });
    const data = await resp.json();

    if (data.success) {
        window.open('<?= base_url('nota/cetak/') ?>' + data.order_id, '_blank');
        location.reload();
    } else {
        alert('Gagal: ' + data.message);
    }
}

async function batalPendingDirect(id, invoiceNo) {
    if (!confirm(`Yakin ingin membatalkan pesanan ${invoiceNo}?`)) return;

    const resp = await fetch('<?= base_url('kasir/batal/') ?>' + id, {
        method: 'POST',
    });
    const data = await resp.json();

    if (data.success) {
        location.reload();
    } else {
        alert('Gagal: ' + data.message);
    }
}
</script>

<?= $this->include('layout/footer') ?>

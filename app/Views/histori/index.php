<?= $this->include('layout/header') ?>

<div class="filter-bar">
    <form method="get" action="<?= base_url('histori') ?>" style="display:flex;gap:12px;align-items:center;">
        <label for="tanggal" style="font-size:0.85rem;font-weight:600;">Tanggal:</label>
        <input type="date" name="tanggal" id="tanggal" class="form-control" style="width:auto;"
               value="<?= esc($tanggal) ?>">
        <button type="submit" class="btn btn-primary" style="padding:8px 16px;">Filter</button>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No. Nota</th>
                    <th>Waktu</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada transaksi pada tanggal ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?= esc($order['invoice_no']) ?></strong></td>
                            <td><?= date('H:i', strtotime($order['created_at'])) ?></td>
                            <td>Rp<?= number_format($order['total'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($order['status'] === 'selesai'): ?>
                                    <span class="badge badge-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Void</span>
                                <?php endif; ?>
                            </td>
                            <td style="display:flex;gap:6px;flex-wrap:wrap;">
                                <a href="<?= base_url('histori/detail/' . $order['id']) ?>" class="btn btn-secondary" style="padding:6px 12px;font-size:0.78rem;">Lihat</a>
                                <a href="<?= base_url('nota/cetak/' . $order['id']) ?>" target="_blank" class="btn btn-accent" style="padding:6px 12px;font-size:0.78rem;">Cetak</a>
                                <?php if ($order['status'] === 'selesai'): ?>
                                    <button class="btn btn-danger" style="padding:6px 12px;font-size:0.78rem;" onclick="voidOrder(<?= $order['id'] ?>)">Void</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Void -->
<div class="modal-overlay" id="modal-void">
    <div class="modal">
        <div class="modal-header">
            <h3>⚠️ Void Transaksi</h3>
            <button class="modal-close" onclick="closeVoid()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="void-reason">Alasan Void (wajib)</label>
                <textarea id="void-reason" class="form-control" rows="3" placeholder="Masukkan alasan void..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeVoid()">Batal</button>
            <button class="btn btn-danger" id="btn-void-confirm" onclick="confirmVoid()">Void Transaksi</button>
        </div>
    </div>
</div>

<script>
let voidOrderId = null;

function voidOrder(id) {
    voidOrderId = id;
    document.getElementById('void-reason').value = '';
    document.getElementById('modal-void').classList.add('show');
}

function closeVoid() {
    document.getElementById('modal-void').classList.remove('show');
    voidOrderId = null;
}

async function confirmVoid() {
    const reason = document.getElementById('void-reason').value.trim();
    if (!reason) {
        alert('Alasan void wajib diisi!');
        return;
    }

    const formData = new FormData();
    formData.append('void_reason', reason);

    const resp = await fetch('<?= base_url('histori/void/') ?>' + voidOrderId, {
        method: 'POST',
        body: formData,
    });
    const data = await resp.json();

    if (data.success) {
        closeVoid();
        location.reload();
    } else {
        alert(data.message);
    }
}
</script>

<?= $this->include('layout/footer') ?>

<?= $this->include('layout/header') ?>

<div class="filter-bar">
    <form method="get" action="<?= base_url('laporan/harian') ?>" style="display:flex;gap:12px;align-items:center;">
        <label for="tanggal" style="font-size:0.85rem;font-weight:600;">Tanggal:</label>
        <input type="date" name="tanggal" id="tanggal" class="form-control" style="width:auto;"
               value="<?= esc($tanggal) ?>">
        <button type="submit" class="btn btn-primary" style="padding:8px 16px;">Lihat</button>
    </form>
    <a href="<?= base_url('laporan/bulanan') ?>" class="btn btn-secondary" style="padding:8px 16px;">📅 Laporan Bulanan</a>
</div>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="sc-value">Rp<?= number_format($summary['total_omzet'], 0, ',', '.') ?></div>
        <div class="sc-label">Total Omzet</div>
    </div>
    <div class="summary-card accent">
        <div class="sc-value"><?= $summary['jumlah_transaksi'] ?></div>
        <div class="sc-label">Jumlah Transaksi</div>
    </div>
    <div class="summary-card">
        <div class="sc-value">Rp<?= number_format($avgTransaksi, 0, ',', '.') ?></div>
        <div class="sc-label">Rata-rata Transaksi</div>
    </div>
</div>

<!-- Menu Terlaris -->
<div class="card">
    <div class="card-header">🏆 Menu Terlaris Hari Ini</div>
    <div class="card-body">
        <?php if (empty($menuTerlaris)): ?>
            <p class="text-muted text-center">Belum ada data.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Menu</th>
                            <th class="text-center">Terjual</th>
                            <th class="text-right">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menuTerlaris as $i => $m): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($m['item_name']) ?></td>
                                <td class="text-center"><?= $m['total_terjual'] ?></td>
                                <td class="text-right">Rp<?= number_format($m['omzet_menu'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->include('layout/footer') ?>

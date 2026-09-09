<?= $this->include('layout/header') ?>

<div class="filter-bar">
    <form method="get" action="<?= base_url('laporan/bulanan') ?>" style="display:flex;gap:12px;align-items:center;">
        <label style="font-size:0.85rem;font-weight:600;">Bulan:</label>
        <select name="bulan" class="form-control" style="width:auto;">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $bulan == $m ? 'selected' : '' ?>>
                    <?= DateTime::createFromFormat('!m', $m)->format('F') ?>
                </option>
            <?php endfor; ?>
        </select>
        <label style="font-size:0.85rem;font-weight:600;">Tahun:</label>
        <select name="tahun" class="form-control" style="width:auto;">
            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn btn-primary" style="padding:8px 16px;">Lihat</button>
    </form>
    <a href="<?= base_url('laporan/harian') ?>" class="btn btn-secondary" style="padding:8px 16px;">📊 Laporan Harian</a>
</div>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="sc-value">Rp<?= number_format($summaryBulan['total_omzet'], 0, ',', '.') ?></div>
        <div class="sc-label">Omzet Bulan Ini</div>
    </div>
    <div class="summary-card accent">
        <div class="sc-value"><?= $summaryBulan['jumlah_transaksi'] ?></div>
        <div class="sc-label">Jumlah Transaksi</div>
    </div>
    <div class="summary-card">
        <div class="sc-value">Rp<?= number_format($prevOmzet, 0, ',', '.') ?></div>
        <div class="sc-label">Omzet Bulan Lalu</div>
    </div>
    <div class="summary-card">
        <?php
            $diff = $summaryBulan['total_omzet'] - $prevOmzet;
            $diffClass = $diff >= 0 ? 'text-success' : 'text-danger';
            $diffSign  = $diff >= 0 ? '+' : '';
        ?>
        <div class="sc-value <?= $diffClass ?>"><?= $diffSign ?>Rp<?= number_format(abs($diff), 0, ',', '.') ?></div>
        <div class="sc-label">Selisih</div>
    </div>
</div>

<!-- Grafik Tren Harian -->
<div class="card mb-3">
    <div class="card-header">📈 Tren Omzet Harian</div>
    <div class="card-body">
        <?php if (empty($trenHarian)): ?>
            <p class="text-muted text-center">Belum ada data.</p>
        <?php else: ?>
            <div class="chart-container">
                <canvas id="chartTren"></canvas>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Menu Terlaris -->
<div class="card">
    <div class="card-header">🏆 Menu Terlaris Bulan Ini</div>
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

<script src="<?= base_url('js/laporan.js') ?>"></script>
<?php if (!empty($trenHarian)): ?>
<script>
    const trenData = <?= json_encode($trenHarian) ?>;
    if (typeof renderTrenChart === 'function') {
        renderTrenChart(trenData);
    }
</script>
<?php endif; ?>

<?= $this->include('layout/footer') ?>

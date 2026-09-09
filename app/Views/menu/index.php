<?= $this->include('layout/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h2 style="font-size:1rem;font-weight:600;">Daftar Menu</h2>
    <a href="<?= base_url('menu/create') ?>" class="btn btn-primary">+ Tambah Menu</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($menuItems)): ?>
                    <tr><td colspan="7" class="text-center text-muted">Belum ada menu.</td></tr>
                <?php else: ?>
                    <?php foreach ($menuItems as $i => $item): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?= base_url('uploads/menu/' . $item['image']) ?>" alt="<?= esc($item['name']) ?>" style="width:42px;height:42px;border-radius:6px;object-fit:cover;box-shadow:0 1px 3px rgba(0,0,0,0.12);">
                                <?php else: ?>
                                    <div style="width:42px;height:42px;border-radius:6px;background:var(--bg-card-alt);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--text-muted);">🍗</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= esc($item['name']) ?></strong></td>
                            <td><?= esc($catMap[$item['category_id']] ?? '-') ?></td>
                            <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td>
                                <?php
                                    $badgeClass = 'badge-secondary';
                                    if ($item['status'] === 'aktif') $badgeClass = 'badge-success';
                                    elseif ($item['status'] === 'habis') $badgeClass = 'badge-warning';
                                    elseif ($item['status'] === 'nonaktif') $badgeClass = 'badge-danger';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= esc($item['status']) ?></span>
                            </td>
                            <td>
                                <a href="<?= base_url('menu/edit/' . $item['id']) ?>" class="btn btn-secondary" style="padding:6px 12px;font-size:0.78rem;">Edit</a>
                                <?php if ($item['status'] === 'aktif'): ?>
                                    <button class="btn btn-accent" style="padding:6px 12px;font-size:0.78rem;" onclick="toggleStatus(<?= $item['id'] ?>, 'habis')">Habis</button>
                                <?php elseif ($item['status'] === 'habis'): ?>
                                    <button class="btn btn-success" style="padding:6px 12px;font-size:0.78rem;" onclick="toggleStatus(<?= $item['id'] ?>, 'aktif')">Aktifkan</button>
                                <?php else: ?>
                                    <button class="btn btn-success" style="padding:6px 12px;font-size:0.78rem;" onclick="toggleStatus(<?= $item['id'] ?>, 'aktif')">Aktifkan</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
async function toggleStatus(id, status) {
    const formData = new FormData();
    formData.append('status', status);

    const resp = await fetch('<?= base_url('menu/toggle-status/') ?>' + id, {
        method: 'POST',
        body: formData,
    });
    const data = await resp.json();
    if (data.success) {
        location.reload();
    } else {
        alert(data.message);
    }
}
</script>

<?= $this->include('layout/footer') ?>

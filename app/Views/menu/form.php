<?= $this->include('layout/header') ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul style="margin:0;padding-left:16px;">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="max-width:600px;">
    <div class="card-header"><?= $menu ? 'Edit Menu' : 'Tambah Menu Baru' ?></div>
    <div class="card-body">
        <form action="<?= $menu ? base_url('menu/update/' . $menu['id']) : base_url('menu/store') ?>" method="post" enctype="multipart/form-data">

            <div class="form-group">
                <label for="name">Nama Menu</label>
                <input type="text" name="name" id="name" class="form-control"
                       value="<?= old('name', $menu['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Kategori</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= old('category_id', $menu['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Harga (Rp)</label>
                <input type="number" name="price" id="price" class="form-control"
                       value="<?= old('price', $menu['price'] ?? '') ?>" min="0" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <?php
                        $currentStatus = old('status', $menu['status'] ?? 'aktif');
                        $statuses = ['aktif', 'habis', 'nonaktif'];
                    ?>
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?= $s ?>" <?= $currentStatus === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Foto Menu</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">
                <small class="text-muted" style="display:block;margin-top:4px;">Format: JPG, PNG, WEBP (Maks 2MB)</small>
                
                <div style="margin-top:10px;">
                    <?php if (!empty($menu['image'])): ?>
                        <img id="img-preview" src="<?= base_url('uploads/menu/' . $menu['image']) ?>" alt="Preview" style="max-width:120px;max-height:120px;border-radius:8px;object-fit:cover;box-shadow:0 2px 6px rgba(0,0,0,0.15);">
                    <?php else: ?>
                        <img id="img-preview" src="" alt="Preview" style="max-width:120px;max-height:120px;border-radius:8px;object-fit:cover;display:none;box-shadow:0 2px 6px rgba(0,0,0,0.15);">
                    <?php endif; ?>
                </div>
            </div>

            <div style="display:flex;gap:12px;margin-top:20px;">
                <button type="submit" class="btn btn-primary"><?= $menu ? 'Simpan Perubahan' : 'Tambah Menu' ?></button>
                <a href="<?= base_url('menu') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>

<script>
function previewImage(input) {
    const preview = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
    </div>
</div>

<?= $this->include('layout/footer') ?>

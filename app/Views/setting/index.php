<?= $this->include('layout/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card" style="max-width:600px;">
    <div class="card-header">⚙️ Pengaturan Warung</div>
    <div class="card-body">
        <form action="<?= base_url('setting/update') ?>" method="post">

            <div class="form-group">
                <label for="nama_warung">Nama Warung</label>
                <input type="text" name="nama_warung" id="nama_warung" class="form-control"
                       value="<?= esc($settings['nama_warung'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" rows="2"><?= esc($settings['alamat'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="no_telp">Nomor Telepon</label>
                <input type="text" name="no_telp" id="no_telp" class="form-control"
                       value="<?= esc($settings['no_telp'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="info_nota">Informasi Nota (ucapan terima kasih, dll)</label>
                <textarea name="info_nota" id="info_nota" class="form-control" rows="2"><?= esc($settings['info_nota'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary mt-2">💾 Simpan Pengaturan</button>
        </form>
    </div>
</div>

<?= $this->include('layout/footer') ?>

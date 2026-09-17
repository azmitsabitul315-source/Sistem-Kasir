<?= $this->include('layout/header') ?>

<div class="kasir-wrapper">
    <!-- Kolom Kiri: Daftar Menu -->
    <div class="menu-section">
        <?php foreach ($categories as $catId => $group): ?>
            <div class="category-title"><?= esc($group['category']['name']) ?></div>
            <div class="menu-grid">
                <?php foreach ($group['items'] as $item): ?>
                    <div class="menu-card"
                         data-id="<?= $item['id'] ?>"
                         data-name="<?= esc($item['name']) ?>"
                         data-price="<?= $item['price'] ?>"
                         data-category="<?= esc($group['category']['name']) ?>"
                         onclick="addToCart(this)">
                        <?php if (!empty($item['image'])): ?>
                            <div class="menu-thumb">
                                <img src="<?= base_url('uploads/menu/' . $item['image']) ?>" alt="<?= esc($item['name']) ?>">
                            </div>
                        <?php else: ?>
                            <div class="menu-thumb placeholder">
                                <?php
                                    $icon = '🍗';
                                    if (mb_stripos($group['category']['name'], 'minum') !== false) $icon = '🥤';
                                    elseif (mb_stripos($group['category']['name'], 'tambahan') !== false) $icon = '🍚';
                                ?>
                                <span><?= $icon ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="menu-info">
                            <div class="menu-name"><?= esc($item['name']) ?></div>
                            <div class="menu-price">Rp<?= number_format($item['price'], 0, ',', '.') ?></div>
                            <?php if ($group['category']['name'] === 'Paket Ayam'): ?>
                                <div class="menu-includes">+ nasi + es teh</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Kolom Kanan: Keranjang -->
    <div class="cart-section">
        <div class="cart-header" style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span>🛒 Keranjang</span>
                <span id="cart-count" style="background:rgba(255,255,255,0.2);padding:2px 10px;border-radius:12px;font-size:0.8rem;">0</span>
            </div>
            <button class="btn-bill-aktif" onclick="openPendingOrdersModal()" style="background:#f39c12;color:#fff;border:none;padding:6px 12px;border-radius:8px;font-size:0.8rem;font-weight:600;display:flex;align-items:center;gap:4px;">
                📋 Bill Aktif <span id="pending-count-badge" class="badge bg-danger" style="background:#c0392b;padding:2px 6px;border-radius:10px;font-size:0.75rem;">0</span>
            </button>
        </div>

        <div id="editing-indicator" style="display:none;background:#fff3cd;color:#856404;padding:8px 12px;font-size:0.85rem;font-weight:600;border-bottom:1px solid #ffeeba;">
            ✏️ Mengedit Bill: <span id="editing-invoice-no"></span>
            <button onclick="cancelEditing()" style="float:right;background:none;border:none;color:#856404;font-weight:bold;cursor:pointer;">&times; Batal Edit</button>
        </div>

        <div class="cart-items" id="cart-items">
            <div class="cart-empty" id="cart-empty">
                Belum ada pesanan.<br>Klik menu untuk menambahkan.
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total</span>
                <span class="total-amount" id="cart-total">Rp0</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px;">
                <button class="btn-simpan-pesanan" id="btn-simpan" onclick="simpanPesananPending()" disabled style="background:#e67e22;color:#fff;border:none;padding:12px;border-radius:8px;font-weight:700;font-size:0.9rem;">
                    📌 Simpan Pesanan
                </button>
                <button class="btn-bayar" id="btn-bayar" onclick="openPayment()" disabled style="padding:12px;border-radius:8px;font-weight:700;font-size:0.9rem;">
                    💰 Bayar Langsung
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bill Aktif / Antrian Pesanan -->
<div class="modal-overlay" id="modal-pending-orders">
    <div class="modal" style="max-width:650px;width:95%;">
        <div class="modal-header" style="background:var(--accent);color:#fff;">
            <h3>📋 Bill Aktif (Pesanan Belum Lunas)</h3>
            <button class="modal-close" onclick="closePendingOrdersModal()" style="color:#fff;">&times;</button>
        </div>
        <div class="modal-body" style="max-height:70vh;overflow-y:auto;padding:16px;">
            <div id="pending-orders-list">
                <div class="text-center text-muted py-4">Memuat bill aktif...</div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closePendingOrdersModal()">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Pembayaran -->
<div class="modal-overlay" id="modal-payment">
    <div class="modal">
        <div class="modal-header">
            <h3>💰 Pembayaran</h3>
            <button class="modal-close" onclick="closePayment()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="pay-invoice-info" style="display:none;background:#f8f9fa;padding:8px 12px;border-radius:6px;margin-bottom:12px;font-size:0.9rem;font-weight:600;color:var(--text-dark);">
                Bill: <span id="pay-invoice-no"></span>
            </div>
            <div class="payment-row total">
                <span>Total</span>
                <span id="pay-total">Rp0</span>
            </div>
            <div class="form-group mt-2">
                <label for="amount-paid">Uang Bayar</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:1.2rem;font-weight:700;color:var(--text-muted);">Rp</span>
                    <input type="text" id="amount-paid" class="form-control form-control-lg"
                           style="padding-left:50px;"
                           placeholder="0" inputmode="numeric" oninput="formatUangBayar(this); calcChange();">
                </div>
            </div>
            <div class="payment-row">
                <span>Kembalian</span>
                <span class="kembalian" id="pay-change">Rp0</span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closePayment()">Batal</button>
            <button class="btn btn-primary btn-lg" id="btn-finish" onclick="finishPayment()" disabled>
                ✅ Selesaikan & Cetak Nota
            </button>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal-overlay" id="modal-success">
    <div class="modal">
        <div class="modal-header">
            <h3>✅ Transaksi Berhasil!</h3>
            <button class="modal-close" onclick="closeSuccess()">&times;</button>
        </div>
        <div class="modal-body text-center">
            <p style="font-size:2.5rem;margin-bottom:12px;">🎉</p>
            <p><strong>No. Nota:</strong> <span id="success-invoice"></span></p>
            <div class="payment-row total" style="justify-content:center;gap:16px;">
                <span>Total: <span id="success-total"></span></span>
            </div>
            <div class="payment-row" style="justify-content:center;gap:16px;">
                <span>Bayar: <span id="success-paid"></span></span>
                <span>Kembali: <span id="success-change" class="text-success"></span></span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeSuccess()">Tutup</button>
            <a class="btn btn-primary" id="btn-cetak-nota" href="#" target="_blank">🖨️ Cetak Nota</a>
        </div>
    </div>
</div>

<script src="<?= base_url('js/kasir.js') ?>"></script>
<?= $this->include('layout/footer') ?>

<!-- Sidebar -->
<nav class="sidebar no-print">
    <div class="sidebar-brand">
        <h2>🍗 Ayam Bakar</h2>
        <small>Point of Sale</small>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="<?= base_url('kasir') ?>" class="<?= (uri_string() == '' || uri_string() == 'kasir') ? 'active' : '' ?>">
                <span class="nav-icon">🛒</span>
                <span>Kasir</span>
            </a>
        </li>
        <li>
            <a href="<?= base_url('histori') ?>" class="<?= (str_starts_with(uri_string(), 'histori')) ? 'active' : '' ?>">
                <span class="nav-icon">📋</span>
                <span>Histori Pesanan</span>
            </a>
        </li>
        <li>
            <a href="<?= base_url('laporan') ?>" class="<?= (str_starts_with(uri_string(), 'laporan')) ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span>Laporan Penjualan</span>
            </a>
        </li>
        <li>
            <a href="<?= base_url('menu') ?>" class="<?= (str_starts_with(uri_string(), 'menu')) ? 'active' : '' ?>">
                <span class="nav-icon">🍽️</span>
                <span>Menu Makanan</span>
            </a>
        </li>
        <li>
            <a href="<?= base_url('setting') ?>" class="<?= (str_starts_with(uri_string(), 'setting')) ? 'active' : '' ?>">
                <span class="nav-icon">⚙️</span>
                <span>Pengaturan</span>
            </a>
        </li>
    </ul>
</nav>

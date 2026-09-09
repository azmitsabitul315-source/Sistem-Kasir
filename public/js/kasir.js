/* ============================================================
   POS Warung Ayam Bakar — kasir.js
   Mengelola keranjang belanja & pembayaran (client-side)
   ============================================================ */

let rawBaseUrl = document.querySelector('meta[name="base-url"]')?.content || (window.location.origin + '/POSayambakar/public/');
if (!rawBaseUrl.endsWith('/')) rawBaseUrl += '/';
const BASE_URL = rawBaseUrl;

let cart = [];

// ========================
// KERANJANG
// ========================

function addToCart(el) {
    const id    = parseInt(el.dataset.id);
    const name  = el.dataset.name;
    const price = parseFloat(el.dataset.price);

    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ id, name, price, qty: 1, note: '' });
    }

    // Animasi klik
    el.style.transform = 'scale(0.93)';
    setTimeout(() => el.style.transform = '', 150);

    renderCart();
}

function changeQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;

    item.qty += delta;
    if (item.qty < 1) {
        removeItem(id);
        return;
    }
    renderCart();
}

function removeItem(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
}

function getTotal() {
    return cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
}

function formatRp(num) {
    return 'Rp' + formatRibuan(num);
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const emptyEl   = document.getElementById('cart-empty');
    const totalEl   = document.getElementById('cart-total');
    const countEl   = document.getElementById('cart-count');
    const btnBayar  = document.getElementById('btn-bayar');

    if (cart.length === 0) {
        container.innerHTML = '<div class="cart-empty" id="cart-empty">Belum ada pesanan.<br>Klik menu untuk menambahkan.</div>';
        totalEl.textContent = 'Rp0';
        countEl.textContent = '0';
        btnBayar.disabled = true;
        return;
    }

    btnBayar.disabled = false;
    const totalItems = cart.reduce((s, i) => s + i.qty, 0);
    countEl.textContent = totalItems;

    let html = '';
    cart.forEach(item => {
        const subtotal = item.price * item.qty;
        html += `
        <div class="cart-item">
            <div class="ci-info">
                <div class="ci-name">${escapeHtml(item.name)}</div>
                <div class="ci-price">${formatRp(item.price)}</div>
            </div>
            <div class="ci-qty">
                <button onclick="changeQty(${item.id}, -1)">−</button>
                <span>${item.qty}</span>
                <button onclick="changeQty(${item.id}, +1)">+</button>
            </div>
            <div class="ci-subtotal">${formatRp(subtotal)}</div>
            <button class="ci-remove" onclick="removeItem(${item.id})" title="Hapus">&times;</button>
        </div>`;
    });

    container.innerHTML = html;
    totalEl.textContent = formatRp(getTotal());
}

// ========================
// PEMBAYARAN
// ========================

function openPayment() {
    if (cart.length === 0) return;

    document.getElementById('pay-total').textContent = formatRp(getTotal());
    document.getElementById('amount-paid').value = '';
    document.getElementById('pay-change').textContent = 'Rp0';
    document.getElementById('pay-change').classList.remove('kurang');
    document.getElementById('btn-finish').disabled = true;
    document.getElementById('modal-payment').classList.add('show');

    setTimeout(() => document.getElementById('amount-paid').focus(), 200);
}

function closePayment() {
    document.getElementById('modal-payment').classList.remove('show');
}

function formatRibuan(angka) {
    // Manual format titik ribuan: 30000 -> 30.000
    let str = String(angka);
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

function formatUangBayar(el) {
    // Ambil posisi cursor
    let cursorPos = el.selectionStart;
    let oldLen = el.value.length;

    // Hanya simpan digit
    let raw = el.value.replace(/\D/g, '');
    if (raw === '' || raw === '0') {
        el.value = '';
        return;
    }

    // Hilangkan leading zeros
    raw = String(parseInt(raw, 10));

    // Format dengan titik ribuan manual
    el.value = formatRibuan(raw);

    // Sesuaikan posisi cursor
    let newLen = el.value.length;
    let diff = newLen - oldLen;
    let newPos = cursorPos + diff;
    if (newPos < 0) newPos = 0;
    el.setSelectionRange(newPos, newPos);
}

function parseUang(str) {
    // Hapus semua karakter non-angka lalu parse
    if (!str) return 0;
    let angka = str.replace(/\D/g, '');
    return angka === '' ? 0 : parseInt(angka, 10);
}

function calcChange() {
    const total = getTotal();
    const paid  = parseUang(document.getElementById('amount-paid').value);
    const change = paid - total;

    const changeEl = document.getElementById('pay-change');
    const btnFinish = document.getElementById('btn-finish');

    if (paid >= total && paid > 0) {
        changeEl.textContent = 'Rp' + formatRibuan(change);
        changeEl.classList.remove('kurang');
        btnFinish.disabled = false;
    } else {
        changeEl.textContent = paid > 0 ? 'Kurang Rp' + formatRibuan(total - paid) : 'Rp0';
        changeEl.classList.add('kurang');
        btnFinish.disabled = true;
    }
}

async function finishPayment() {
    const total = getTotal();
    const amountPaid = parseUang(document.getElementById('amount-paid').value);

    if (amountPaid < total) {
        alert('Uang bayar kurang!');
        return;
    }

    // Siapkan data POST
    const items = cart.map(item => ({
        menu_item_id: item.id,
        qty: item.qty,
        note: item.note || '',
    }));

    const formData = new FormData();
    items.forEach((item, i) => {
        formData.append(`items[${i}][menu_item_id]`, item.menu_item_id);
        formData.append(`items[${i}][qty]`, item.qty);
        formData.append(`items[${i}][note]`, item.note);
    });
    formData.append('amount_paid', amountPaid);

    // Tambah CSRF token
    const csrfName = document.querySelector('meta[name="csrf-name"]')?.content;
    const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content;
    if (csrfName && csrfHash) {
        formData.append(csrfName, csrfHash);
    }

    try {
        const resp = await fetch(BASE_URL + 'kasir/proses', {
            method: 'POST',
            body: formData,
        });
        const data = await resp.json();

        if (data.success) {
            closePayment();

            // Tampilkan modal sukses
            document.getElementById('success-invoice').textContent = data.invoice_no;
            document.getElementById('success-total').textContent = formatRp(data.total);
            document.getElementById('success-paid').textContent = formatRp(data.amount_paid);
            document.getElementById('success-change').textContent = formatRp(data.change);
            document.getElementById('btn-cetak-nota').href = BASE_URL + 'nota/cetak/' + data.order_id;
            document.getElementById('modal-success').classList.add('show');

            // Reset keranjang
            cart = [];
            renderCart();
        } else {
            alert('Gagal: ' + data.message);
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan.');
        console.error(err);
    }
}

function closeSuccess() {
    document.getElementById('modal-success').classList.remove('show');
}

// ========================
// UTILITY
// ========================

function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Inisialisasi
renderCart();

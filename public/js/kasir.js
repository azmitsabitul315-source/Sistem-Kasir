/* ============================================================
   POS Warung Ayam Bakar — kasir.js
   Mengelola keranjang belanja, bill aktif & pembayaran (client-side)
   ============================================================ */

let rawBaseUrl = document.querySelector('meta[name="base-url"]')?.content || (window.location.origin + '/POSayambakar/public/');
if (!rawBaseUrl.endsWith('/')) rawBaseUrl += '/';
const BASE_URL = rawBaseUrl;

let cart = [];
let currentOrderId = null;   // ID order jika sedang mengedit/membayar bill pending
let currentInvoiceNo = null; // No invoice jika sedang mengedit bill pending

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
    const totalEl   = document.getElementById('cart-total');
    const countEl   = document.getElementById('cart-count');
    const btnSimpan = document.getElementById('btn-simpan');
    const btnBayar  = document.getElementById('btn-bayar');

    // Tampilkan / sembunyikan indikator mode edit
    const editingInd = document.getElementById('editing-indicator');
    if (currentOrderId && currentInvoiceNo) {
        editingInd.style.display = 'block';
        document.getElementById('editing-invoice-no').textContent = currentInvoiceNo;
    } else {
        editingInd.style.display = 'none';
    }

    if (cart.length === 0) {
        container.innerHTML = '<div class="cart-empty" id="cart-empty">Belum ada pesanan.<br>Klik menu untuk menambahkan.</div>';
        totalEl.textContent = 'Rp0';
        countEl.textContent = '0';
        if (btnSimpan) btnSimpan.disabled = true;
        if (btnBayar)  btnBayar.disabled  = true;
        return;
    }

    if (btnSimpan) btnSimpan.disabled = false;
    if (btnBayar)  btnBayar.disabled  = false;

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

function cancelEditing() {
    currentOrderId = null;
    currentInvoiceNo = null;
    cart = [];
    renderCart();
}

// ========================
// BILL AKTIF / PENDING
// ========================

async function fetchPendingOrders() {
    try {
        const resp = await fetch(BASE_URL + 'kasir/pending');
        const data = await resp.json();

        if (data.success) {
            const badge = document.getElementById('pending-count-badge');
            if (badge) badge.textContent = data.orders.length;
            renderPendingOrdersList(data.orders);
        }
    } catch (err) {
        console.error('Gagal mengambil bill aktif:', err);
    }
}

function openPendingOrdersModal() {
    document.getElementById('modal-pending-orders').classList.add('show');
    fetchPendingOrders();
}

function closePendingOrdersModal() {
    document.getElementById('modal-pending-orders').classList.remove('show');
}

function renderPendingOrdersList(orders) {
    const container = document.getElementById('pending-orders-list');
    if (!container) return;

    if (orders.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-4" style="text-align:center;padding:24px;color:#777;">Tidak ada bill aktif (pesanan pending).</div>';
        return;
    }

    let html = '';
    orders.forEach(order => {
        let itemsListHtml = '';
        if (order.items && order.items.length > 0) {
            itemsListHtml = order.items.map(i => `<li>${escapeHtml(i.item_name)} x${i.qty}</li>`).join('');
        }

        const formattedDate = new Date(order.created_at).toLocaleString('id-ID', {
            hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short'
        });

        html += `
        <div class="pending-card" style="background:#fff;border:1px solid #e0d6cc;border-radius:10px;padding:14px;margin-bottom:12px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;border-bottom:1px dashed #ddd;padding-bottom:6px;">
                <div>
                    <strong style="font-size:1rem;color:var(--primary);">${escapeHtml(order.invoice_no)}</strong>
                    <span style="font-size:0.75rem;color:#777;margin-left:8px;">🕒 ${formattedDate}</span>
                </div>
                <span class="badge" style="background:#f39c12;color:#fff;padding:3px 8px;border-radius:6px;font-size:0.75rem;font-weight:600;">Pending</span>
            </div>
            
            <ul style="font-size:0.85rem;color:#444;margin:6px 0 10px 18px;padding:0;">
                ${itemsListHtml}
            </ul>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:8px;border-top:1px solid #eee;">
                <div>
                    <span style="font-size:0.8rem;color:#777;">Total Bill:</span>
                    <strong style="font-size:1.1rem;color:var(--text-dark);margin-left:4px;">${formatRp(parseFloat(order.total))}</strong>
                </div>
                <div style="display:flex;gap:6px;">
                    <button onclick="bayarPendingOrder(${order.id}, '${order.invoice_no}', ${order.total})" style="background:#27ae60;color:#fff;border:none;padding:6px 12px;border-radius:6px;font-size:0.85rem;font-weight:600;cursor:pointer;">
                        💰 Bayar
                    </button>
                    <button onclick="editPendingOrder(${order.id})" style="background:#3498db;color:#fff;border:none;padding:6px 10px;border-radius:6px;font-size:0.85rem;font-weight:600;cursor:pointer;">
                        ✏️ Edit
                    </button>
                    <a href="${BASE_URL}nota/cetak/${order.id}" target="_blank" style="background:#7f8c8d;color:#fff;border:none;padding:6px 10px;border-radius:6px;font-size:0.85rem;font-weight:600;text-decoration:none;display:inline-block;">
                        🖨️ Struk
                    </a>
                    <button onclick="batalPendingOrder(${order.id}, '${order.invoice_no}')" style="background:#e74c3c;color:#fff;border:none;padding:6px 10px;border-radius:6px;font-size:0.85rem;font-weight:600;cursor:pointer;">
                        ❌ Batal
                    </button>
                </div>
            </div>
        </div>`;
    });

    container.innerHTML = html;
}

// SIMPAN PESANAN KE BILL AKTIF (PESAN DULU)
async function simpanPesananPending() {
    if (cart.length === 0) return;

    const items = cart.map(item => ({
        menu_item_id: item.id,
        qty: item.qty,
        note: item.note || '',
    }));

    const formData = new FormData();
    if (currentOrderId) {
        formData.append('order_id', currentOrderId);
    }
    items.forEach((item, i) => {
        formData.append(`items[${i}][menu_item_id]`, item.menu_item_id);
        formData.append(`items[${i}][qty]`, item.qty);
        formData.append(`items[${i}][note]`, item.note);
    });

    const csrfName = document.querySelector('meta[name="csrf-name"]')?.content;
    const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content;
    if (csrfName && csrfHash) {
        formData.append(csrfName, csrfHash);
    }

    try {
        const resp = await fetch(BASE_URL + 'kasir/simpan', {
            method: 'POST',
            body: formData,
        });
        const data = await resp.json();

        if (data.success) {
            alert(`✅ Pesanan tersimpan ke Bill Aktif!\nNo. Nota: ${data.invoice_no}\nTotal: ${formatRp(data.total)}`);
            cancelEditing();
            fetchPendingOrders();
        } else {
            alert('Gagal: ' + data.message);
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan.');
        console.error(err);
    }
}

// EDIT BILL PENDING
async function editPendingOrder(orderId) {
    try {
        const resp = await fetch(BASE_URL + 'kasir/detail/' + orderId);
        const data = await resp.json();

        if (data.success) {
            currentOrderId = data.order.id;
            currentInvoiceNo = data.order.invoice_no;

            // Load items ke keranjang
            cart = data.items.map(item => ({
                id: parseInt(item.menu_item_id),
                name: item.item_name,
                price: parseFloat(item.price),
                qty: parseInt(item.qty),
                note: item.note || ''
            }));

            closePendingOrdersModal();
            renderCart();
        } else {
            alert('Gagal mengambil detail pesanan: ' + data.message);
        }
    } catch (err) {
        console.error('Error edit pending order:', err);
    }
}

// BAYAR PESANAN PENDING DARI MODAL BILL AKTIF
function bayarPendingOrder(orderId, invoiceNo, total) {
    currentOrderId = orderId;
    currentInvoiceNo = invoiceNo;

    closePendingOrdersModal();

    document.getElementById('pay-invoice-info').style.display = 'block';
    document.getElementById('pay-invoice-no').textContent = invoiceNo;
    document.getElementById('pay-total').textContent = formatRp(total);
    document.getElementById('amount-paid').value = '';
    document.getElementById('pay-change').textContent = 'Rp0';
    document.getElementById('pay-change').classList.remove('kurang');
    document.getElementById('btn-finish').disabled = true;

    document.getElementById('modal-payment').classList.add('show');
    setTimeout(() => document.getElementById('amount-paid').focus(), 200);
}

// BATALKAN PESANAN PENDING
async function batalPendingOrder(orderId, invoiceNo) {
    if (!confirm(`Yakin ingin membatalkan pesanan ${invoiceNo}?`)) return;

    try {
        const csrfName = document.querySelector('meta[name="csrf-name"]')?.content;
        const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content;
        const formData = new FormData();
        if (csrfName && csrfHash) {
            formData.append(csrfName, csrfHash);
        }

        const resp = await fetch(BASE_URL + 'kasir/batal/' + orderId, {
            method: 'POST',
            body: formData
        });
        const data = await resp.json();

        if (data.success) {
            fetchPendingOrders();
        } else {
            alert('Gagal membatalkan: ' + data.message);
        }
    } catch (err) {
        console.error('Error batal pending order:', err);
    }
}

// ========================
// PEMBAYARAN
// ========================

function openPayment() {
    const total = getTotal();
    if (total <= 0 && !currentOrderId) return;

    if (currentOrderId && currentInvoiceNo) {
        document.getElementById('pay-invoice-info').style.display = 'block';
        document.getElementById('pay-invoice-no').textContent = currentInvoiceNo;
    } else {
        document.getElementById('pay-invoice-info').style.display = 'none';
    }

    document.getElementById('pay-total').textContent = formatRp(total);
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

function formatUangBayar(el) {
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

function parseUang(str) {
    if (!str) return 0;
    let angka = str.replace(/\D/g, '');
    return angka === '' ? 0 : parseInt(angka, 10);
}

function calcChange() {
    let total = getTotal();
    // Jika tidak ada item di keranjang tapi sedang bayar bill pending yang ada totalnya di UI
    if (total <= 0 && currentOrderId) {
        const totalTxt = document.getElementById('pay-total').textContent;
        total = parseUang(totalTxt);
    }

    const paid  = parseUang(document.getElementById('amount-paid').value);
    const change = paid - total;

    const changeEl  = document.getElementById('pay-change');
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
    let total = getTotal();
    if (total <= 0 && currentOrderId) {
        const totalTxt = document.getElementById('pay-total').textContent;
        total = parseUang(totalTxt);
    }

    const amountPaid = parseUang(document.getElementById('amount-paid').value);

    if (amountPaid < total) {
        alert('Uang bayar kurang!');
        return;
    }

    const formData = new FormData();
    if (currentOrderId) {
        formData.append('order_id', currentOrderId);
    }

    if (cart.length > 0) {
        const items = cart.map(item => ({
            menu_item_id: item.id,
            qty: item.qty,
            note: item.note || '',
        }));
        items.forEach((item, i) => {
            formData.append(`items[${i}][menu_item_id]`, item.menu_item_id);
            formData.append(`items[${i}][qty]`, item.qty);
            formData.append(`items[${i}][note]`, item.note);
        });
    }

    formData.append('amount_paid', amountPaid);

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

            // Reset keranjang & state edit
            cancelEditing();
            fetchPendingOrders();
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
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function checkUrlEditParam() {
    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('edit');
    if (editId) {
        editPendingOrder(parseInt(editId));
    }
}

// Inisialisasi
renderCart();
fetchPendingOrders();
checkUrlEditParam();

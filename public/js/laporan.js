/* ============================================================
   POS Warung Ayam Bakar — laporan.js
   Simple bar chart using Canvas (vanilla JS, no library needed)
   ============================================================ */

function renderTrenChart(data) {
    const canvas = document.getElementById('chartTren');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const container = canvas.parentElement;

    // Responsive sizing
    canvas.width  = container.offsetWidth;
    canvas.height = 300;

    const W = canvas.width;
    const H = canvas.height;
    const padding = { top: 30, right: 20, bottom: 60, left: 80 };

    const chartW = W - padding.left - padding.right;
    const chartH = H - padding.top - padding.bottom;

    // Data
    const labels = data.map(d => {
        const date = new Date(d.tanggal);
        return date.getDate().toString();
    });
    const values = data.map(d => parseFloat(d.omzet_harian));
    const maxVal = Math.max(...values, 1);

    // Clear
    ctx.clearRect(0, 0, W, H);

    // Background
    ctx.fillStyle = '#FDFCFB';
    ctx.fillRect(0, 0, W, H);

    // Grid lines
    const gridLines = 5;
    ctx.strokeStyle = '#E0D6CC';
    ctx.lineWidth = 0.5;
    ctx.font = '11px Poppins, sans-serif';
    ctx.fillStyle = '#777';
    ctx.textAlign = 'right';

    for (let i = 0; i <= gridLines; i++) {
        const y = padding.top + (chartH / gridLines) * i;
        const val = maxVal - (maxVal / gridLines) * i;

        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(W - padding.right, y);
        ctx.stroke();

        ctx.fillText('Rp' + Math.round(val).toLocaleString('id-ID'), padding.left - 8, y + 4);
    }

    // Bars
    const barCount = labels.length;
    const barGap = Math.max(4, chartW / barCount * 0.3);
    const barWidth = (chartW - barGap * (barCount + 1)) / barCount;

    const gradient = ctx.createLinearGradient(0, padding.top, 0, H - padding.bottom);
    gradient.addColorStop(0, '#C0392B');
    gradient.addColorStop(1, '#E67E22');

    values.forEach((val, i) => {
        const barH = (val / maxVal) * chartH;
        const x = padding.left + barGap + i * (barWidth + barGap);
        const y = padding.top + chartH - barH;

        // Bar
        ctx.fillStyle = gradient;
        const radius = Math.min(4, barWidth / 2);
        roundRect(ctx, x, y, barWidth, barH, radius);
        ctx.fill();

        // Hover value on top
        if (val > 0) {
            ctx.fillStyle = '#333';
            ctx.font = '9px Poppins, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Rp' + Math.round(val / 1000) + 'rb', x + barWidth / 2, y - 6);
        }

        // Label
        ctx.fillStyle = '#777';
        ctx.font = '10px Poppins, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(labels[i], x + barWidth / 2, H - padding.bottom + 16);
    });

    // X-axis label
    ctx.fillStyle = '#777';
    ctx.font = '11px Poppins, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('Tanggal', W / 2, H - 8);
}

function roundRect(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + w - r, y);
    ctx.quadraticCurveTo(x + w, y, x + w, y + r);
    ctx.lineTo(x + w, y + h);
    ctx.lineTo(x, y + h);
    ctx.lineTo(x, y + r);
    ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath();
}

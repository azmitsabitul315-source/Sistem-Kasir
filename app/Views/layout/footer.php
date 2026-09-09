        </div><!-- /.page-content -->
    </div><!-- /.main-content -->
</div><!-- /.app-wrapper -->

<!-- Clock Script -->
<script>
function updateClock() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateEl = document.getElementById('topbar-date');
    const clockEl = document.getElementById('topbar-clock');
    if (dateEl) dateEl.textContent = now.toLocaleDateString('id-ID', options);
    if (clockEl) clockEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
updateClock();
setInterval(updateClock, 1000);
</script>
</body>
</html>

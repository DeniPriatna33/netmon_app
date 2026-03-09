    <!-- Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; 2024 <a href="#">My Company</a>.</strong> All rights reserved.
    </footer>
</div>
<!-- ./wrapper -->

<!-- JavaScript Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Base URL for JavaScript -->
<script>
var BASE_URL = '<?= base_url() ?>';
</script>

<!-- Main JavaScript -->
<script src="<?= base_url('public/assets/js/main.js') ?>"></script>

<!-- CSRF Token untuk semua AJAX POST -->
<script>
var csrfName  = '<?= $this->security->get_csrf_token_name() ?>';
var csrfHash  = '<?= $this->security->get_csrf_hash() ?>';
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        if (settings.type === 'POST') {
            if (typeof settings.data === 'string') {
                settings.data += '&' + csrfName + '=' + encodeURIComponent(csrfHash);
            } else if (settings.data instanceof FormData) {
                settings.data.append(csrfName, csrfHash);
            } else {
                if (!settings.data) settings.data = {};
                settings.data[csrfName] = csrfHash;
            }
        }
    }
});
</script>

<!-- Script Notifikasi Flashdata -->
<?php if($this->session->flashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= $this->session->flashdata('success') ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '<?= $this->session->flashdata('error') ?>'
        });
    </script>
<?php endif; ?>

<!-- Yield Script untuk halaman spesifik -->
<?= $scripts ?? '' ?>

</body>
</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if($this->session->flashdata('success')): ?>
<script>
Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $this->session->flashdata('success') ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
</script>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
<script>
Swal.fire({ icon: 'error', title: 'Gagal', text: '<?= $this->session->flashdata('error') ?>' });
</script>
<?php endif; ?>

</body>
</html>

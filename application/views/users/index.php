<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0"><?= $title; ?></h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Data User</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" onclick="tambah()">
                            <i class="fas fa-plus"></i> Tambah User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable_user" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modal_form" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form User</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="form_data">
                <div class="modal-body">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username">
                        <small class="text-danger error-text" id="err_username"></small>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Nama Lengkap">
                        <small class="text-danger error-text" id="err_full_name"></small>
                    </div>
                    <div class="form-group">
                        <label>Password <span class="text-danger" id="pw_required">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Min. 6 karakter">
                        <small class="text-muted" id="pw_hint" style="display:none;">Kosongkan jika tidak ingin mengubah password.</small>
                        <small class="text-danger error-text" id="err_password"></small>
                    </div>
                    <div class="form-group">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="operator">Operator</option>
                        </select>
                        <small class="text-danger error-text" id="err_role"></small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
var table;
var csrfTokenName = '<?= $this->security->get_csrf_token_name() ?>';
var csrfToken = '<?= $this->security->get_csrf_hash() ?>';

$(function() {
    // Initialize DataTable
    table = $('#dataTable_user').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url("users/ajax_list") ?>',
            type: 'POST',
            data: function(d) {
                d[csrfTokenName] = csrfToken;
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5, orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        drawCallback: function() {
            // Update CSRF token after each draw
            if (this.api().context[0].jqXHR && this.api().context[0].jqXHR.getResponseHeader('X-CSRF-TOKEN')) {
                csrfToken = this.api().context[0].jqXHR.getResponseHeader('X-CSRF-TOKEN');
            }
        }
    });

    // Form submission handler
    $('#form_data').on('submit', function(e) {
        e.preventDefault();
        $('.error-text').text('');
        
        $.ajax({
            url: '<?= base_url("users/ajax_save") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    $('#modal_form').modal('hide');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    if (res.errors) {
                        $.each(res.errors, function(key, val) {
                            $('#err_' + key).text(val);
                        });
                    } else if (res.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan pada sistem.'
                });
            }
        });
    });
});

function tambah() {
    $('#form_data')[0].reset();
    $('#id').val('');
    $('.error-text').text('');
    $('#pw_required').show();
    $('#pw_hint').hide();
    $('#password').val('');
    $('.modal-title').text('Tambah User');
    $('#is_active').prop('checked', true);
    $('#modal_form').modal('show');
}

function edit(id) {
    $.ajax({
        url: '<?= base_url("users/ajax_get") ?>',
        type: 'POST',
        data: {
            id: id,
            [csrfTokenName]: csrfToken
        },
        dataType: 'json',
        success: function(row) {
            if (row) {
                $('#form_data')[0].reset();
                $('.error-text').text('');
                $('#id').val(row.id);
                $('#username').val(row.username);
                $('#full_name').val(row.full_name);
                $('#role').val(row.role);
                $('#is_active').prop('checked', row.is_active == 1 || row.is_active == true);
                $('#pw_required').hide();
                $('#pw_hint').show();
                $('#password').val('');
                $('.modal-title').text('Edit User');
                $('#modal_form').modal('show');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal mengambil data user.'
            });
        }
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Yakin hapus user ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("users/ajax_delete") ?>',
                type: 'POST',
                data: {
                    id: id,
                    [csrfTokenName]: csrfToken
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menghapus user.'
                    });
                }
            });
        }
    });
}
</script>

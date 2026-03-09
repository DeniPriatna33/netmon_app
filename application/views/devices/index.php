<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0"><?= $title; ?></h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Perangkat</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Data Perangkat</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" onclick="tambah()">
                            <i class="fas fa-plus"></i> Tambah Perangkat
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>IP Address</th>
                                    <th>Hostname</th>
                                    <th>Deskripsi</th>
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
                <h5 class="modal-title">Form Perangkat</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="form_data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label>IP Address <span class="text-danger">*</span></label>
                        <input type="text" name="ip_address" id="ip_address" class="form-control" placeholder="192.168.1.1">
                        <small class="text-danger error-text" id="err_ip_address"></small>
                    </div>
                    <div class="form-group">
                        <label>Hostname <span class="text-danger">*</span></label>
                        <input type="text" name="hostname" id="hostname" class="form-control" placeholder="Mikrotik Core">
                        <small class="text-danger error-text" id="err_hostname"></small>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="2" placeholder="Keterangan perangkat"></textarea>
                        <small class="text-danger error-text" id="err_description"></small>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var table;

$(function() {
    table = $('#dataTable').DataTable({
        processing: true, serverSide: true,
        ajax: { 
            url: '<?= base_url("devices/ajax_list") ?>', 
            type: 'POST',
            data: function(d) {
                // Ensure DataTables uses fresh token (if you had CSRF on read, you'd add it here)
                // d[NetmonApp.CSRF.tokenName] = NetmonApp.CSRF.token; 
                // But generally CI3 Datatables needs specific config or CSRF disabled 
                // for ajax_list if we don't return new tokens. Let's just pass the global one for now.
                d['<?= $this->security->get_csrf_token_name() ?>'] = $('input[name="<?= $this->security->get_csrf_token_name() ?>"]').length ? $('input[name="<?= $this->security->get_csrf_token_name() ?>"]').val() : '';
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false },
            { data: 1 }, { data: 2 }, { data: 3 }, { data: 4 },
            { data: 5, orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    $('#form_data').on('submit', function(e) {
        e.preventDefault();
        $('.error-text').text('');
        
        // Refresh token before submitting
        NetmonApp.CSRF.refresh(function(csrf_data) {
            
            // Re-inject updated CSRF to the form
            if ($('input[name="' + csrf_data.csrf_token_name + '"]').length === 0) {
                $('#form_data').append('<input type="hidden" name="' + csrf_data.csrf_token_name + '" value="' + csrf_data.csrf_token + '">');
            } else {
                $('input[name="' + csrf_data.csrf_token_name + '"]').val(csrf_data.csrf_token);
            }

            $.ajax({
                url: '<?= base_url("devices/ajax_save") ?>', type: 'POST',
                data: $('#form_data').serialize(), dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        $('#modal_form').modal('hide');
                        table.ajax.reload();
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    } else {
                        if (res.errors) {
                            $.each(res.errors, function(key, val) { $('#err_' + key).text(val); });
                        }
                    }
                }
            });
        });
    });
});

function tambah() {
    $('#form_data')[0].reset(); $('#id').val(''); $('.error-text').text('');
    $('.modal-title').text('Tambah Perangkat');
    
    // Refresh token when opening modal
    NetmonApp.CSRF.refresh(function(csrf_data) {
        if ($('input[name="' + csrf_data.csrf_token_name + '"]').length === 0) {
            $('#form_data').append('<input type="hidden" name="' + csrf_data.csrf_token_name + '" value="' + csrf_data.csrf_token + '">');
        } else {
            $('input[name="' + csrf_data.csrf_token_name + '"]').val(csrf_data.csrf_token);
        }
        $('#modal_form').modal('show');
    });
}

function edit(id) {
    NetmonApp.CSRF.refresh(function(csrf_data) {
        $.ajax({
            url: '<?= base_url("devices/ajax_get") ?>', type: 'POST',
            data: { 
                id: id,
                [csrf_data.csrf_token_name]: csrf_data.csrf_token 
            }, 
            dataType: 'json',
            success: function(row) {
                $('#form_data')[0].reset(); $('.error-text').text('');
                $('#id').val(row.id);
                $('#ip_address').val(row.ip_address);
                $('#hostname').val(row.hostname);
                $('#description').val(row.description);
                $('.modal-title').text('Edit Perangkat');
                
                if ($('input[name="' + csrf_data.csrf_token_name + '"]').length === 0) {
                    $('#form_data').append('<input type="hidden" name="' + csrf_data.csrf_token_name + '" value="' + csrf_data.csrf_token + '">');
                } else {
                    $('input[name="' + csrf_data.csrf_token_name + '"]').val(csrf_data.csrf_token);
                }

                $('#modal_form').modal('show');
            }
        });
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Yakin hapus perangkat ini?', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            NetmonApp.CSRF.refresh(function(csrf_data) {
                $.ajax({
                    url: '<?= base_url("devices/ajax_delete") ?>', type: 'POST',
                    data: { 
                        id: id,
                        [csrf_data.csrf_token_name]: csrf_data.csrf_token
                    }, 
                    dataType: 'json',
                    success: function(res) {
                        table.ajax.reload();
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    }
                });
            });
        }
    });
}
</script>

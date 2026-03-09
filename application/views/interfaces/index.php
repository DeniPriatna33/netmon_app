<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0"><?= $title; ?></h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Interface</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Daftar Interface</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" onclick="tambah()">
                            <i class="fas fa-plus"></i> Tambah Data
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Device (IP)</th>
                                    <th>Interface Name</th>
                                    <th>Speed</th>
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
                <h5 class="modal-title">Form Interface</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="form_data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label>Device <span class="text-danger">*</span></label>
                        <select name="device_id" id="device_id" class="form-control">
                            <?php foreach ($devices as $val => $label): ?>
                                <option value="<?= $val ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger error-text" id="err_device_id"></small>
                    </div>
                    <div class="form-group">
                        <label>Interface Name <span class="text-danger">*</span></label>
                        <input type="text" name="interface_name" id="interface_name" class="form-control" placeholder="eth0, GigabitEthernet0/1">
                        <small class="text-danger error-text" id="err_interface_name"></small>
                    </div>
                    <div class="form-group">
                        <label>Speed (bps)</label>
                        <input type="number" name="speed" id="speed" class="form-control" placeholder="1000000000">
                        <small class="text-danger error-text" id="err_speed"></small>
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

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
var table;

$(function() {
    table = $('#dataTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: '<?= base_url("interfaces/ajax_list") ?>', type: 'POST' },
        columns: [
            { data: 0, orderable: false, searchable: false },
            { data: 1 }, { data: 2 }, { data: 3 },
            { data: 4, orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
    });

    $('#form_data').on('submit', function(e) {
        e.preventDefault();
        $('.error-text').text('');
        $.ajax({
            url: '<?= base_url("interfaces/ajax_save") ?>', type: 'POST',
            data: $(this).serialize(), dataType: 'json',
            success: function(res) {
                if (res.status) {
                    $('#modal_form').modal('hide');
                    table.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                } else {
                    $.each(res.errors, function(key, val) { $('#err_' + key).text(val); });
                }
            }
        });
    });
});

function tambah() {
    $('#form_data')[0].reset(); $('#id').val(''); $('.error-text').text('');
    $('.modal-title').text('Tambah Interface');
    $('#modal_form').modal('show');
}

function edit(id) {
    $.ajax({
        url: '<?= base_url("interfaces/ajax_get") ?>', type: 'POST',
        data: { id: id }, dataType: 'json',
        success: function(row) {
            $('#form_data')[0].reset(); $('.error-text').text('');
            $('#id').val(row.id);
            $('#device_id').val(row.device_id);
            $('#interface_name').val(row.interface_name);
            $('#speed').val(row.speed);
            $('.modal-title').text('Edit Interface');
            $('#modal_form').modal('show');
        }
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Yakin hapus data ini?', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("interfaces/ajax_delete") ?>', type: 'POST',
                data: { id: id }, dataType: 'json',
                success: function(res) {
                    table.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            });
        }
    });
}
</script>

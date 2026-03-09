<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0"><?= $title; ?></h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Alerts</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bell mr-1"></i> Riwayat Peringatan</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Perangkat</th>
                                    <th>Pesan</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Diselesaikan</th>
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

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function() {
    $('#dataTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: '<?= base_url("alerts/ajax_list") ?>', type: 'POST' },
        columns: [
            { data: 0, orderable: false, searchable: false },
            { data: 1 }, { data: 2 }, { data: 3 }, { data: 4 }, { data: 5 }
        ],
        order: [[4, 'desc']]
    });
});
</script>

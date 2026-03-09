<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Dashboard Monitoring</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="stat-total"><?= $summary['total'] ?></h3>
                            <p>Total Device</p>
                        </div>
                        <div class="icon"><i class="fas fa-server"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="stat-up"><?= $summary['up'] ?></h3>
                            <p>Device UP</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="stat-down"><?= $summary['down'] ?></h3>
                            <p>Device DOWN</p>
                        </div>
                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="stat-unknown"><?= $summary['unknown'] ?></h3>
                            <p>Unknown</p>
                        </div>
                        <div class="icon"><i class="fas fa-question-circle"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Device Status Table -->
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-network-wired mr-1"></i> Status Perangkat</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover" id="device-table">
                                <thead>
                                    <tr>
                                        <th>Hostname</th>
                                        <th>IP Address</th>
                                        <th>Status</th>
                                        <th>Latency</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="device-tbody">
                                    <?php foreach ($devices as $d): ?>
                                    <tr id="dev-<?= $d->id ?>">
                                        <td><?= htmlspecialchars($d->hostname) ?></td>
                                        <td><code><?= htmlspecialchars($d->ip_address) ?></code></td>
                                        <td>
                                            <?php if ($d->status === 'UP'): ?>
                                                <span class="badge badge-success">UP</span>
                                            <?php elseif ($d->status === 'DOWN'): ?>
                                                <span class="badge badge-danger">DOWN</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">UNKNOWN</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="latency-cell">-</td>
                                        <td>
                                            <button class="btn btn-xs btn-info" onclick="showChart(<?= $d->id ?>, '<?= htmlspecialchars($d->hostname) ?>')">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Active Alerts -->
                <div class="col-md-4">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-bell mr-1"></i> Peringatan Aktif</h3>
                            <span class="badge badge-danger float-right" id="alert-count"><?= count($active_alerts) ?></span>
                        </div>
                        <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                            <ul class="list-group list-group-flush" id="alert-list">
                                <?php if (empty($active_alerts)): ?>
                                    <li class="list-group-item text-muted text-center">Tidak ada peringatan aktif</li>
                                <?php else: ?>
                                    <?php foreach ($active_alerts as $alert): ?>
                                    <li class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between">
                                            <strong class="text-danger"><?= htmlspecialchars($alert->hostname) ?></strong>
                                            <small class="text-muted"><?= $alert->created_at ?></small>
                                        </div>
                                        <small><?= htmlspecialchars($alert->message) ?></small>
                                    </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latency Chart Modal -->
            <div class="modal fade" id="chartModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="chartModalTitle">Latency Chart</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div id="latencyChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
var REFRESH_MS = <?= $refresh_ms ?>;
var chart = null;

// Auto-refresh dashboard data
function fetchDashboardData() {
    $.getJSON('<?= base_url("api/get_dashboard_data") ?>', function(res) {
        if (!res) return;
        // Update summary cards
        $('#stat-total').text(res.summary.total);
        $('#stat-up').text(res.summary.up);
        $('#stat-down').text(res.summary.down);
        $('#stat-unknown').text(res.summary.unknown);

        // Update device statuses in table
        if (res.devices) {
            $.each(res.devices, function(i, d) {
                var row = $('#dev-' + d.id);
                if (row.length) {
                    var badge = d.status === 'UP' ? '<span class="badge badge-success">UP</span>' :
                                d.status === 'DOWN' ? '<span class="badge badge-danger">DOWN</span>' :
                                '<span class="badge badge-secondary">UNKNOWN</span>';
                    row.find('td:eq(2)').html(badge);
                }
            });
        }

        // Update alerts
        if (res.alerts !== undefined) {
            $('#alert-count').text(res.alerts.length);
            var html = '';
            if (res.alerts.length === 0) {
                html = '<li class="list-group-item text-muted text-center">Tidak ada peringatan aktif</li>';
            } else {
                $.each(res.alerts, function(i, a) {
                    html += '<li class="list-group-item list-group-item-action">' +
                        '<div class="d-flex justify-content-between">' +
                        '<strong class="text-danger">' + a.hostname + '</strong>' +
                        '<small class="text-muted">' + a.created_at + '</small>' +
                        '</div><small>' + a.message + '</small></li>';
                });
            }
            $('#alert-list').html(html);
        }
    });
}

function showChart(deviceId, hostname) {
    $('#chartModalTitle').text('Latency - ' + hostname);
    $('#chartModal').modal('show');

    $.getJSON('<?= base_url("api/get_device_chart/") ?>' + deviceId, function(res) {
        var categories = [];
        var latencies = [];

        // Data comes DESC, reverse for chronological display
        var data = (res || []).reverse();
        $.each(data, function(i, row) {
            categories.push(row.recorded_at);
            latencies.push(parseFloat(row.ping_latency_ms) || 0);
        });

        if (chart) chart.destroy();

        chart = new ApexCharts(document.querySelector('#latencyChart'), {
            chart: { type: 'area', height: 350 },
            series: [{ name: 'Latency (ms)', data: latencies }],
            xaxis: { categories: categories, labels: { rotate: -45, style: { fontSize: '10px' } } },
            yaxis: { title: { text: 'ms' }, min: 0 },
            colors: ['#007bff'],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.1 } },
            tooltip: { y: { formatter: function(val) { return val + ' ms'; } } }
        });
        chart.render();
    });
}

// Start auto-refresh
setInterval(fetchDashboardData, REFRESH_MS);
</script>

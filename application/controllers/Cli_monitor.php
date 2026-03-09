<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CLI-only controller for network polling and housekeeping.
 *
 * Usage (crontab):
 *   * * * * * /usr/bin/php /var/www/html/netmon/index.php cli_monitor run_polling
 *   0 0 * * * /usr/bin/php /var/www/html/netmon/index.php cli_monitor cleanup_logs
 */
class Cli_monitor extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // Restrict to CLI only
        if ( ! is_cli()) {
            show_error('Akses hanya via CLI.', 403);
        }

        $this->load->model('Device_model', 'device_model');
        $this->load->model('Monitoring_log_model', 'log_model');
        $this->load->model('Alert_model', 'alert_model');
        $this->load->library('notifier');
    }

    /**
     * Main polling function.
     * Pings all active devices and updates their status.
     */
    public function run_polling()
    {
        $devices   = $this->device_model->get_active_devices();
        $threshold = $this->config->item('ping_fail_threshold');
        $timeout   = $this->config->item('ping_timeout');
        $count     = $this->config->item('ping_count');

        echo "[" . date('Y-m-d H:i:s') . "] Polling " . count($devices) . " device(s)...\n";

        foreach ($devices as $device) {
            $result = $this->_ping($device->ip_address, $timeout, $count);

            if ($result['success']) {
                // Ping succeeded
                $this->device_model->update($device->id, [
                    'status'          => 'UP',
                    'ping_fail_count' => 0,
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);

                // Resolve any active alerts for this device
                if ($this->alert_model->has_active_alert($device->id)) {
                    $this->alert_model->resolve_by_device($device->id);
                    echo "  [{$device->ip_address}] RECOVERED - alerts resolved\n";

                    $this->notifier->send(
                        "RESOLVED: {$device->hostname} ({$device->ip_address}) kembali UP. Latency: {$result['latency']}ms"
                    );
                }

                // Log the result
                $this->log_model->insert([
                    'device_id'       => $device->id,
                    'status_up'       => TRUE,
                    'ping_latency_ms' => $result['latency'],
                ]);

                echo "  [{$device->ip_address}] UP - {$result['latency']}ms\n";

            } else {
                // Ping failed
                $fail_count = $device->ping_fail_count + 1;

                $update_data = [
                    'ping_fail_count' => $fail_count,
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];

                if ($fail_count >= $threshold) {
                    $update_data['status'] = 'DOWN';

                    // Create alert if not already active
                    if ( ! $this->alert_model->has_active_alert($device->id)) {
                        $message = "{$device->hostname} ({$device->ip_address}) DOWN setelah {$fail_count}x gagal ping.";
                        $this->alert_model->insert([
                            'device_id' => $device->id,
                            'message'   => $message,
                        ]);

                        $this->notifier->send("ALERT: " . $message);
                        echo "  [{$device->ip_address}] DOWN - ALERT CREATED\n";
                    } else {
                        echo "  [{$device->ip_address}] DOWN - fail #{$fail_count}\n";
                    }
                } else {
                    echo "  [{$device->ip_address}] TIMEOUT - fail #{$fail_count}/{$threshold}\n";
                }

                $this->device_model->update($device->id, $update_data);

                // Log the failure
                $this->log_model->insert([
                    'device_id'       => $device->id,
                    'status_up'       => FALSE,
                    'ping_latency_ms' => NULL,
                ]);
            }
        }

        echo "[" . date('Y-m-d H:i:s') . "] Polling complete.\n";
    }

    /**
     * Housekeeping: Delete old monitoring logs.
     */
    public function cleanup_logs()
    {
        $days    = $this->config->item('log_retention_days');
        $deleted = $this->log_model->cleanup_old_logs($days);
        echo "[" . date('Y-m-d H:i:s') . "] Cleanup: {$deleted} log(s) older than {$days} days deleted.\n";
    }

    /**
     * Perform a ping to the given IP address.
     * Returns ['success' => bool, 'latency' => float|null]
     */
    private function _ping($ip, $timeout = 2, $count = 1)
    {
        // Detect OS for ping command syntax
        $os = PHP_OS_FAMILY;

        if ($os === 'Windows') {
            $cmd = "ping -n {$count} -w " . ($timeout * 1000) . " " . escapeshellarg($ip);
        } else {
            $cmd = "ping -c {$count} -W {$timeout} " . escapeshellarg($ip);
        }

        $output = [];
        $code   = 0;
        exec($cmd, $output, $code);

        $output_str = implode("\n", $output);

        if ($code === 0) {
            // Extract latency from ping output
            $latency = NULL;
            if (preg_match('/time[=<](\d+\.?\d*)/', $output_str, $matches)) {
                $latency = (float) $matches[1];
            }
            return ['success' => TRUE, 'latency' => $latency];
        }

        return ['success' => FALSE, 'latency' => NULL];
    }
}

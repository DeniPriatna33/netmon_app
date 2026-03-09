<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CLI-only controller for running database migrations.
 *
 * Usage:
 *   php index.php migrate current   -> Migrate to $config['migration_version']
 *   php index.php migrate latest    -> Migrate to latest available migration
 */
class Migrate extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        if ( ! is_cli()) {
            show_error('Akses hanya via CLI.', 403);
        }

        $this->load->library('migration');
    }

    public function current()
    {
        if ($this->migration->current() === FALSE) {
            echo "Migration error: " . $this->migration->error_string() . "\n";
        } else {
            echo "Migration to current version completed successfully.\n";
        }
    }

    public function latest()
    {
        if ($this->migration->latest() === FALSE) {
            echo "Migration error: " . $this->migration->error_string() . "\n";
        } else {
            echo "Migration to latest version completed successfully.\n";
        }
    }
}

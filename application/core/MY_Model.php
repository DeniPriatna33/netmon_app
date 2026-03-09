<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Model - Base model for reusable CRUD operations.
 *
 * Each child model only needs to set:
 *   - $table          : table name
 *   - $primary_key    : primary key column (default 'id')
 *   - $fillable       : columns allowed for insert/update
 *   - $searchable     : columns used in DataTables search
 *   - $orderable      : columns mapped by DataTables column index
 *   - $validation_rules: CI form_validation rules array
 */
class MY_Model extends CI_Model {

    protected $table       = '';
    protected $primary_key = 'id';
    protected $fillable    = [];
    protected $searchable  = [];
    protected $orderable   = [];

    // Override in child to define validation rules
    protected $validation_rules = [];

    // ─── Basic CRUD ────────────────────────────────────────

    public function get_all()
    {
        return $this->db->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, [$this->primary_key => $id])->row();
    }

    public function insert($data)
    {
        $data = $this->_filter_fillable($data);
        $this->db->insert($this->table, $data);
        return $this->db->insert_id($this->table . '_' . $this->primary_key . '_seq');
    }

    public function update($id, $data)
    {
        $data = $this->_filter_fillable($data);
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table);
    }

    // ─── DataTables Server-Side ────────────────────────────

    public function get_datatables($additional_where = [])
    {
        $this->_build_datatable_query($additional_where);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result();
    }

    public function count_filtered($additional_where = [])
    {
        $this->_build_datatable_query($additional_where);
        return $this->db->count_all_results();
    }

    public function count_all($additional_where = [])
    {
        $this->db->from($this->table);
        foreach ($additional_where as $key => $val) {
            $this->db->where($key, $val);
        }
        return $this->db->count_all_results();
    }

    // ─── Validation ────────────────────────────────────────

    public function get_validation_rules()
    {
        return $this->validation_rules;
    }

    public function validate()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules($this->validation_rules);
        return $this->form_validation->run();
    }

    // ─── Dropdown helper ───────────────────────────────────

    public function dropdown($value_field = 'id', $label_field = 'name', $where = [])
    {
        if ( ! empty($where)) {
            $this->db->where($where);
        }
        $rows = $this->db->select("{$value_field}, {$label_field}")
                         ->get($this->table)
                         ->result();
        $options = ['' => '-- Pilih --'];
        foreach ($rows as $row) {
            $options[$row->$value_field] = $row->$label_field;
        }
        return $options;
    }

    // ─── Private helpers ───────────────────────────────────

    private function _filter_fillable($data)
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    private function _build_datatable_query($additional_where = [])
    {
        $this->_datatable_select();
        $this->db->from($this->table);
        $this->_datatable_join();

        foreach ($additional_where as $key => $val) {
            $this->db->where($key, $val);
        }

        // Search (using ILIKE for PostgreSQL case-insensitive search)
        $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        if ($search !== '') {
            $this->db->group_start();
            foreach ($this->searchable as $i => $col) {
                $method = ($i === 0) ? 'where' : 'or_where';
                $this->db->$method("{$col} ILIKE", '%' . $search . '%');
            }
            $this->db->group_end();
        }

        // Order
        if (isset($_POST['order'])) {
            $col_idx = $_POST['order'][0]['column'];
            $dir     = $_POST['order'][0]['dir'];
            if (isset($this->orderable[$col_idx]) && $this->orderable[$col_idx] !== '') {
                $this->db->order_by($this->orderable[$col_idx], $dir);
            }
        }
    }

    /**
     * Override in child to customize the SELECT clause (e.g. for JOINs).
     */
    protected function _datatable_select()
    {
        $this->db->select("{$this->table}.*");
    }

    /**
     * Override in child to add JOINs for DataTables.
     */
    protected function _datatable_join()
    {
        // No joins by default
    }
}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Local_activation
{
    protected $CI;
    protected $table = 'local_activation';
    protected $provided_code_hash = null;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->config('local_activation');
        $this->provided_code_hash = $this->CI->config->item('local_activation_code_hash');
    }

    protected function ensure_table()
    {
        if (!$this->CI->db->table_exists($this->table)) {
            $sql = "CREATE TABLE `{$this->table}` (
                `id` tinyint unsigned NOT NULL,
                `is_activated` tinyint(1) NOT NULL DEFAULT 0,
                `activated_at` datetime NULL DEFAULT NULL,
                `updated_at` datetime NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $this->CI->db->query($sql);
        }
    }

    protected function record()
    {
        $this->ensure_table();
        return $this->CI->db->where('id', 1)->get($this->table)->row_array();
    }

    public function is_activated()
    {
        $record = $this->record();
        return !empty($record) && (int) $record['is_activated'] === 1;
    }

    public function get_status()
    {
        $record = $this->record();
        return array(
            'active'       => !empty($record) && (int) $record['is_activated'] === 1,
            'activated_at' => !empty($record['activated_at']) ? $record['activated_at'] : null,
        );
    }

    public function activate($activation_code)
    {
        if (!is_string($this->provided_code_hash) || $this->provided_code_hash === '') {
            return false;
        }

        $activation_code = trim((string) $activation_code);
        if ($activation_code === '' || !password_verify($activation_code, $this->provided_code_hash)) {
            return false;
        }

        $this->ensure_table();
        $now = date('Y-m-d H:i:s');
        $record = $this->record();
        $data = array(
            'is_activated' => 1,
            'activated_at' => $record && !empty($record['activated_at']) ? $record['activated_at'] : $now,
            'updated_at'   => $now,
        );

        if ($record) {
            return (bool) $this->CI->db->where('id', 1)->update($this->table, $data);
        }

        $data['id'] = 1;
        return (bool) $this->CI->db->insert($this->table, $data);
    }
}

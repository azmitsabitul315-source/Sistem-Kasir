<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['key', 'value'];
    protected $useTimestamps = true;

    /**
     * Ambil nilai setting berdasarkan key
     */
    public function getValue(string $key, string $default = ''): string
    {
        $row = $this->where('key', $key)->first();
        return $row ? $row['value'] : $default;
    }

    /**
     * Set nilai setting
     */
    public function setValue(string $key, string $value): bool
    {
        $existing = $this->where('key', $key)->first();
        if ($existing) {
            return $this->update($existing['id'], ['value' => $value]);
        }
        return (bool) $this->insert(['key' => $key, 'value' => $value]);
    }

    /**
     * Ambil semua settings sebagai key => value array
     */
    public function getAllSettings(): array
    {
        $rows = $this->findAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }
}

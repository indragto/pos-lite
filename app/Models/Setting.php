<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';

    /**
     * Get all settings as key-value pairs
     */
    public function getAllSettings(): array
    {
        $settings = $this->findAll([], 'key ASC');
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['key']] = $setting['value'];
        }

        return $result;
    }

    /**
     * Get a single setting value
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = $this->queryOne(
            "SELECT value FROM settings WHERE key = :key",
            ['key' => $key]
        );

        return $setting ? $setting['value'] : $default;
    }

    /**
     * Update or insert a setting
     */
    public function setSetting(string $key, string $value): bool
    {
        $existing = $this->getSetting($key);

        if ($existing !== null) {
            return $this->db->update(
                $this->table,
                ['value' => $value],
                'key = :key',
                ['key' => $key]
            ) > 0;
        }

        $this->db->insert($this->table, [
            'key' => $key,
            'value' => $value
        ]);

        return true;
    }

    /**
     * Update multiple settings
     */
    public function updateMultiple(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->setSetting($key, $value);
        }
    }
}

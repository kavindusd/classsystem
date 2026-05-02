<?php

class SiteSettingModel extends Model {
    protected string $table = 'site_settings';

    public function getSetting(string $key, string $default = ''): string {
        $row = $this->query(
            "SELECT value FROM {$this->table} WHERE `key` = ? LIMIT 1",
            [$key]
        )->fetch();
        return $row ? (string)$row['value'] : $default;
    }

    public function setSetting(string $key, string $value): void {
        $exists = $this->query(
            "SELECT id FROM {$this->table} WHERE `key` = ? LIMIT 1",
            [$key]
        )->fetch();

        if ($exists) {
            $this->query(
                "UPDATE {$this->table} SET value = ? WHERE `key` = ?",
                [$value, $key]
            );
        } else {
            $this->insert(['key' => $key, 'value' => $value]);
        }
    }

    public function getAllAsMap(): array {
        $rows = $this->findAll();
        $map  = [];
        foreach ($rows as $row) {
            $map[$row['key']] = $row['value'];
        }
        return $map;
    }
}
<?php

class AdminModel extends Model {
    protected string $table = 'admins';

    public function findByUserId(int $userId): array|false {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1",
            [$userId]
        )->fetch();
    }
}
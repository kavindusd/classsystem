<?php

class NotificationModel extends Model {
    protected string $table = 'notifications';

    public function getForUser(int $userId): array {
        return $this->query(
            "SELECT * FROM {$this->table}
             WHERE recipient_id = ?
             ORDER BY created_at DESC",
            [$userId]
        )->fetchAll();
    }

    public function getUnreadCount(int $userId): int {
        return (int) $this->query(
            "SELECT COUNT(*) FROM {$this->table}
             WHERE recipient_id = ? AND is_read = 0",
            [$userId]
        )->fetchColumn();
    }

    public function markAllRead(int $userId): void {
        $this->query(
            "UPDATE {$this->table} SET is_read = 1 WHERE recipient_id = ?",
            [$userId]
        );
    }
}
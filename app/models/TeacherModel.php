<?php

class TeacherModel extends Model {
    protected string $table = 'teachers';

    public function findByTeacherId(string $teacherId): array|false {
        return $this->query(
            "SELECT u.id, u.name, u.email, u.phone, u.password, u.role, t.id as teacher_row_id, t.teacher_id, t.is_first_login
             FROM {$this->table} t
             JOIN users u ON u.id = t.user_id
             WHERE t.teacher_id = ? LIMIT 1",
            [$teacherId]
        )->fetch();
    }

    public function findByUserId(int $userId): array|false {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1",
            [$userId]
        )->fetch();
    }

    public function markFirstLoginDone(int $teacherRowId): bool {
        return $this->update($teacherRowId, ['is_first_login' => 0]);
    }
}
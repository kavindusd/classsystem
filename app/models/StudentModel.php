<?php

class StudentModel extends Model {
    protected string $table = 'students';

    public function findByStudentId(string $studentId): array|false {
        return $this->query(
            "SELECT u.id, u.name, u.email, u.phone, u.password, u.role, s.id as student_row_id, s.student_id
             FROM {$this->table} s
             JOIN users u ON u.id = s.user_id
             WHERE s.student_id = ? LIMIT 1",
            [$studentId]
        )->fetch();
    }

    public function findByUserId(int $userId): array|false {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1",
            [$userId]
        )->fetch();
    }

    public function createStudent(int $userId, string $studentId): int {
        return $this->insert([
            'user_id'    => $userId,
            'student_id' => $studentId,
        ]);
    }
}
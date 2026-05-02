<?php

/**
 * Individual student grades per exam.
 * Teachers write, students read only their own.
 */
class GradeModel extends Model {
    protected string $table = 'grades';

    public function getByExam(int $examId): array {
        return $this->query(
            "SELECT g.*, u.name as student_name
             FROM {$this->table} g
             JOIN students s ON s.id = g.student_id
             JOIN users u ON u.id = s.user_id
             WHERE g.exam_id = ?",
            [$examId]
        )->fetchAll();
    }

    public function getForStudent(int $examId, int $studentId): array|false {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE exam_id = ? AND student_id = ?",
            [$examId, $studentId]
        )->fetch();
    }
}

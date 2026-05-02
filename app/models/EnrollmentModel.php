<?php

class EnrollmentModel extends Model {
    protected string $table = 'enrollments';

    public function findEnrollment(int $studentId, int $courseId): array|false {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE student_id = ? AND course_id = ? LIMIT 1",
            [$studentId, $courseId]
        )->fetch();
    }

    public function getByStudent(int $studentId): array {
        return $this->query(
            "SELECT e.*, c.name as course_name, c.subject, c.grade,
                    u.name as teacher_name
             FROM {$this->table} e
             JOIN courses c ON c.id = e.course_id
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             WHERE e.student_id = ?
             ORDER BY e.enrolled_at DESC",
            [$studentId]
        )->fetchAll();
    }

    public function getByCourse(int $courseId): array {
        return $this->query(
            "SELECT e.*, u.name as student_name, s.student_id as student_code, u.email
             FROM {$this->table} e
             JOIN students s ON s.id = e.student_id
             JOIN users u ON u.id = s.user_id
             WHERE e.course_id = ?
             ORDER BY u.name",
            [$courseId]
        )->fetchAll();
    }
}
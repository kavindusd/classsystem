<?php

/**
 * Exam marking sheets — created by teachers per course.
 */
class ExamModel extends Model {
    protected string $table = 'exams';

    public function getByCourse(int $courseId): array {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE course_id = ? ORDER BY created_at DESC",
            [$courseId]
        )->fetchAll();
    }
}

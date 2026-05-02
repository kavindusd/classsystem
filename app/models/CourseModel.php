<?php

class CourseModel extends Model {
    protected string $table = 'courses';

    public function getAllWithTeacher(): array {
        return $this->query(
            "SELECT c.*, u.name as teacher_name, u.profile_image as teacher_image, t.teacher_id
             FROM {$this->table} c
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             ORDER BY c.created_at DESC"
        )->fetchAll();
    }

    public function getByTeacher(int $teacherId): array {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE teacher_id = ? ORDER BY created_at DESC",
            [$teacherId]
        )->fetchAll();
    }

    public function search(string $keyword, string $subject = '', string $grade = ''): array {
        $sql    = "SELECT c.*, u.name as teacher_name, u.profile_image as teacher_image
                   FROM {$this->table} c
                   JOIN teachers t ON t.id = c.teacher_id
                   JOIN users u ON u.id = t.user_id
                   WHERE c.status = 'active'
                   AND (c.name LIKE ? OR c.subject LIKE ? OR u.name LIKE ?)";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%"];

        if ($subject) {
            $sql    .= " AND c.subject = ?";
            $params[] = $subject;
        }
        if ($grade) {
            $sql    .= " AND c.grade = ?";
            $params[] = $grade;
        }

        return $this->query($sql, $params)->fetchAll();
    }
}
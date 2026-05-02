<?php

class GradeController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $courseId = (int) Request::get('course_id', 0);
        $db       = Database::getInstance();

        // All courses for dropdown
        $courses = $db->query(
            "SELECT c.id, c.name, c.subject, c.grade, u.name as teacher_name
             FROM courses c
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             ORDER BY c.name"
        )->fetchAll();

        $exams  = [];
        $grades = [];

        if ($courseId) {
            // Exams for selected course
            $stmt = $db->prepare(
                "SELECT ex.*, u.name as created_by_name
                 FROM exams ex
                 JOIN teachers t ON t.id = ex.created_by
                 JOIN users u ON u.id = t.user_id
                 WHERE ex.course_id = ?
                 ORDER BY ex.created_at DESC"
            );
            $stmt->execute([$courseId]);
            $exams = $stmt->fetchAll();

            // All grades for those exams
            if (!empty($exams)) {
                $examIds      = implode(',', array_column($exams, 'id'));
                $grades       = $db->query(
                    "SELECT g.*, ex.title as exam_title,
                            u.name as student_name, s.student_id
                     FROM grades g
                     JOIN exams ex ON ex.id = g.exam_id
                     JOIN students s ON s.id = g.student_id
                     JOIN users u ON u.id = s.user_id
                     WHERE g.exam_id IN ({$examIds})
                     ORDER BY ex.title, u.name"
                )->fetchAll();
            }
        }

        $this->view('admin/grades', [
            'courses'          => $courses,
            'exams'            => $exams,
            'grades'           => $grades,
            'filter_course_id' => $courseId,
        ], 'admin_layout');
    }
}

<?php

class EnrollmentController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $courseId = (int) Request::get('course_id', 0);
        $db       = Database::getInstance();

        // All courses for the filter dropdown
        $courses = $db->query(
            "SELECT c.id, c.name, c.subject, c.grade, u.name as teacher_name,
                    (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as enrolled_count
             FROM courses c
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             ORDER BY c.name"
        )->fetchAll();

        // Enrollments filtered by course
        $enrollments = [];
        if ($courseId) {
            $stmt = $db->prepare(
                "SELECT e.*, u.name as student_name, s.student_id,
                        c.name as course_name, c.subject
                 FROM enrollments e
                 JOIN students s ON s.id = e.student_id
                 JOIN users u ON u.id = s.user_id
                 JOIN courses c ON c.id = e.course_id
                 WHERE e.course_id = ?
                 ORDER BY u.name"
            );
            $stmt->execute([$courseId]);
            $enrollments = $stmt->fetchAll();
        } else {
            // Show all enrollments
            $enrollments = $db->query(
                "SELECT e.*, u.name as student_name, s.student_id,
                        c.name as course_name, c.subject
                 FROM enrollments e
                 JOIN students s ON s.id = e.student_id
                 JOIN users u ON u.id = s.user_id
                 JOIN courses c ON c.id = e.course_id
                 ORDER BY e.enrolled_at DESC"
            )->fetchAll();
        }

        $this->view('admin/enrollments', [
            'enrollments'      => $enrollments,
            'courses'          => $courses,
            'filter_course_id' => $courseId,
        ], 'admin_layout');
    }

    public function remove(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $db  = Database::getInstance();
        $row = $db->prepare("SELECT id FROM enrollments WHERE id = ? LIMIT 1");
        $row->execute([(int)$id]);

        if (!$row->fetch()) {
            Session::flash('error', 'Enrollment not found.');
            $this->redirect('admin/enrollments');
        }

        $db->prepare("DELETE FROM enrollments WHERE id = ?")->execute([(int)$id]);

        Session::flash('success', 'Enrollment removed.');
        $this->redirect('admin/enrollments');
    }
}

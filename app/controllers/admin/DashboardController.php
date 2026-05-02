<?php

class DashboardController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $db = Database::getInstance();

        $stats = [
            'total_students' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
            'total_teachers' => $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn(),
            'total_courses'  => $db->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
            'pending_slips'  => $db->query("SELECT COUNT(*) FROM slips WHERE status = 'pending'")->fetchColumn(),
        ];

        $recentSlips = $db->query(
            "SELECT s.*, u.name as student_name, c.name as course_name
             FROM slips s
             JOIN students st ON st.id = s.student_id
             JOIN users u ON u.id = st.user_id
             JOIN courses c ON c.id = s.course_id
             WHERE s.status = 'pending'
             ORDER BY s.submitted_at DESC LIMIT 5"
        )->fetchAll();

        $this->view('admin/dashboard', [
            'stats'       => $stats,
            'recentSlips' => $recentSlips,
        ], 'admin_layout');
    }
}
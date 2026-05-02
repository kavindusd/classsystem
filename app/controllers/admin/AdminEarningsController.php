<?php

class AdminEarningsController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $db = Database::getInstance();
        $selectedMonth = Request::get('month', date('Y-m'));

        // 1. Total Earnings Summary (Approved Slips only)
        $stmt = $db->prepare("
            SELECT 
                SUM(c.price) as total_collected,
                SUM(c.institute_cost) as total_institute,
                SUM(c.teacher_commission) as total_teacher_cut
            FROM slips s
            JOIN courses c ON c.id = s.course_id
            WHERE s.status = 'approved' AND s.slip_month = ?
        ");
        $stmt->execute([$selectedMonth]);
        $summary = $stmt->fetch();

        // 2. Earnings by Course
        $stmt = $db->prepare("
            SELECT 
                c.name, 
                c.subject, 
                c.grade,
                COUNT(s.id) as approved_slips,
                SUM(c.price) as collected,
                SUM(c.institute_cost) as institute_share,
                SUM(c.teacher_commission) as teacher_share
            FROM courses c
            LEFT JOIN slips s ON s.course_id = c.id AND s.status = 'approved' AND s.slip_month = ?
            GROUP BY c.id
            ORDER BY collected DESC
        ");
        $stmt->execute([$selectedMonth]);
        $byCourse = $stmt->fetchAll();

        // 3. Earnings by Teacher
        $stmt = $db->prepare("
            SELECT 
                u.name as teacher_name,
                COUNT(s.id) as total_students,
                SUM(c.price) as total_collected,
                SUM(c.teacher_commission) as total_teacher_due
            FROM teachers t
            JOIN users u ON u.id = t.user_id
            JOIN courses c ON c.teacher_id = t.id
            LEFT JOIN slips s ON s.course_id = c.id AND s.status = 'approved' AND s.slip_month = ?
            GROUP BY t.id
            ORDER BY total_teacher_due DESC
        ");
        $stmt->execute([$selectedMonth]);
        $byTeacher = $stmt->fetchAll();

        $this->view('admin/earnings', [
            'summary'   => $summary,
            'byCourse'  => $byCourse,
            'byTeacher' => $byTeacher,
            'selectedMonth' => $selectedMonth
        ], 'admin_layout');
    }
}

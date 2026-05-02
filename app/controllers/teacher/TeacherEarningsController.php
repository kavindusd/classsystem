<?php

class TeacherEarningsController extends Controller {

    public function index(): void {
        (new RoleMiddleware('teacher'))->handle();

        $user         = $this->currentUser();
        $teacherModel = new TeacherModel();
        $teacher      = $teacherModel->findByUserId($user['id']);

        if (!$teacher) $this->abort(403);

        $db = Database::getInstance();
        $selectedMonth = Request::get('month', date('Y-m'));

        // 1. Monthly Summary
        $stmt = $db->prepare("
            SELECT 
                COUNT(s.id) as approved_slips,
                SUM(c.teacher_commission) as total_earnings
            FROM slips s
            JOIN courses c ON c.id = s.course_id
            WHERE c.teacher_id = ? AND s.status = 'approved' AND s.slip_month = ?
        ");
        $stmt->execute([$teacher['id'], $selectedMonth]);
        $summary = $stmt->fetch();

        // 2. Earnings by Course for this month
        $stmt = $db->prepare("
            SELECT 
                c.name,
                c.subject,
                c.grade,
                COUNT(s.id) as students_paid,
                SUM(c.teacher_commission) as earnings
            FROM courses c
            LEFT JOIN slips s ON s.course_id = c.id AND s.status = 'approved' AND s.slip_month = ?
            WHERE c.teacher_id = ?
            GROUP BY c.id
            ORDER BY earnings DESC
        ");
        $stmt->execute([$selectedMonth, $teacher['id']]);
        $byCourse = $stmt->fetchAll();

        $this->view('teacher/earnings', [
            'summary'       => $summary,
            'byCourse'      => $byCourse,
            'selectedMonth' => $selectedMonth,
            'teacher'       => $teacher
        ], 'teacher_layout');
    }
}

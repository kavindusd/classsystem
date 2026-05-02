<?php

/**
 * Teacher dashboard
 */
class DashboardController extends Controller {

    public function index(): void {
        (new RoleMiddleware('teacher'))->handle();

        $user         = $this->currentUser();
        $teacherModel = new TeacherModel();
        $teacher      = $teacherModel->findByUserId($user['id']);

        if (!$teacher) $this->abort(403);

        $db = Database::getInstance();

        // 1. My Courses Count
        $courseCount = $db->prepare("SELECT COUNT(*) FROM courses WHERE teacher_id = ? AND status = 'active'");
        $courseCount->execute([$teacher['id']]);
        $courseCount = (int) $courseCount->fetchColumn();

        // 2. Total Students Enrolled across all my courses
        $studentCount = $db->prepare(
            "SELECT COUNT(DISTINCT e.student_id) 
             FROM enrollments e 
             JOIN courses c ON c.id = e.course_id 
             WHERE c.teacher_id = ?"
        );
        $studentCount->execute([$teacher['id']]);
        $studentCount = (int) $studentCount->fetchColumn();

        // 3. Today's Classes
        $today = date('Y-m-d');
        $todayClasses = $db->prepare(
            "SELECT s.*, c.name as course_name, c.subject 
             FROM schedules s 
             JOIN courses c ON c.id = s.course_id 
             WHERE c.teacher_id = ? AND s.class_date = ? 
             ORDER BY s.start_time ASC"
        );
        $todayClasses->execute([$teacher['id'], $today]);
        $todayClasses = $todayClasses->fetchAll();

        // 4. Unread Notifications
        $notifModel  = new NotificationModel();
        $unreadCount = $notifModel->getUnreadCount($user['id']);
        
        // 5. Recent Notifications
        $recentNotifs = $db->prepare(
            "SELECT * FROM notifications WHERE recipient_id = ? ORDER BY created_at DESC LIMIT 5"
        );
        $recentNotifs->execute([$user['id']]);
        $recentNotifs = $recentNotifs->fetchAll();

        // 6. My Earnings (Current Month)
        $currentMonth = date('Y-m');
        $earningsData = $db->prepare("
            SELECT 
                SUM(c.teacher_commission) as total_earnings
            FROM slips s
            JOIN courses c ON c.id = s.course_id
            WHERE c.teacher_id = ? AND s.status = 'approved' AND s.slip_month = ?
        ");
        $earningsData->execute([$teacher['id'], $currentMonth]);
        $totalEarnings = (float) $earningsData->fetchColumn();

        $courseEarnings = $db->prepare("
            SELECT 
                c.name,
                c.subject,
                c.grade,
                COUNT(s.id) as approved_students,
                SUM(c.teacher_commission) as course_total
            FROM courses c
            LEFT JOIN slips s ON s.course_id = c.id AND s.status = 'approved' AND s.slip_month = ?
            WHERE c.teacher_id = ?
            GROUP BY c.id
            ORDER BY course_total DESC
        ");
        $courseEarnings->execute([$currentMonth, $teacher['id']]);
        $courseEarnings = $courseEarnings->fetchAll();

        $this->view('teacher/dashboard', [
            'user'           => $user,
            'teacher'        => $teacher,
            'courseCount'    => $courseCount,
            'studentCount'   => $studentCount,
            'todayClasses'   => $todayClasses,
            'unreadCount'    => $unreadCount,
            'recentNotifs'   => $recentNotifs,
            'totalEarnings'  => $totalEarnings,
            'courseEarnings' => $courseEarnings,
            'currentMonth'   => $currentMonth,
        ], 'teacher_layout');
    }
}

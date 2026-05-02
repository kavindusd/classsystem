<?php

/**
 * Student dashboard — summary of enrolled courses, pending slips, and unread notifications.
 */
class DashboardController extends Controller {

    public function index(): void {
        (new RoleMiddleware('student'))->handle();

        $user         = $this->currentUser();
        $studentModel = new StudentModel();
        $student      = $studentModel->findByUserId($user['id']);

        if (!$student) $this->abort(403);

        $db = Database::getInstance();

        // Enrolled courses count
        $enrolledCount = $db->prepare(
            "SELECT COUNT(*) FROM enrollments WHERE student_id = ?"
        );
        $enrolledCount->execute([$student['id']]);
        $enrolledCount = (int) $enrolledCount->fetchColumn();

        // Pending slips count
        $pendingSlips = $db->prepare(
            "SELECT COUNT(*) FROM slips WHERE student_id = ? AND status = 'pending'"
        );
        $pendingSlips->execute([$student['id']]);
        $pendingSlips = (int) $pendingSlips->fetchColumn();

        // Rejected slips count (needs resubmission)
        $rejectedSlips = $db->prepare(
            "SELECT COUNT(*) FROM slips WHERE student_id = ? AND status = 'rejected'"
        );
        $rejectedSlips->execute([$student['id']]);
        $rejectedSlips = (int) $rejectedSlips->fetchColumn();

        // Unread notifications
        $notifModel  = new NotificationModel();
        $unreadCount = $notifModel->getUnreadCount($user['id']);

        // Enrolled courses with current month slip status
        $currentMonth = date('Y-m');
        $stmt = $db->prepare(
            "SELECT c.id, c.name, c.subject, c.grade, c.price,
                    u.name as teacher_name,
                    e.enrolled_at,
                    (SELECT status FROM slips s
                     WHERE s.student_id = e.student_id
                       AND s.course_id = c.id
                       AND s.slip_month = ?
                     LIMIT 1) as this_month_slip_status
             FROM enrollments e
             JOIN courses c ON c.id = e.course_id
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             WHERE e.student_id = ?
             ORDER BY c.name"
        );
        $stmt->execute([$currentMonth, $student['id']]);
        $enrolledCourses = $stmt->fetchAll();

        // Recent notifications (top 5)
        $recentNotifs = $db->prepare(
            "SELECT * FROM notifications WHERE recipient_id = ?
             ORDER BY created_at DESC LIMIT 5"
        );
        $recentNotifs->execute([$user['id']]);
        $recentNotifs = $recentNotifs->fetchAll();

        $this->view('student/dashboard', [
            'student'        => $student,
            'user'           => $user,
            'enrolledCount'  => $enrolledCount,
            'pendingSlips'   => $pendingSlips,
            'rejectedSlips'  => $rejectedSlips,
            'unreadCount'    => $unreadCount,
            'enrolledCourses'=> $enrolledCourses,
            'recentNotifs'   => $recentNotifs,
            'currentMonth'   => $currentMonth,
        ], 'student_layout');
    }
}

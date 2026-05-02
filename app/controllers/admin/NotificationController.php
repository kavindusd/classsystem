<?php

class NotificationController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $admin = $this->currentUser();
        $db    = Database::getInstance();

        // Load all teachers and students for targeting
        // NOTE: admin/notifications.php expects teacher_id and student_id to be present.
        $teachers = $db->query(
            "SELECT t.id, t.teacher_id, u.name, u.email
             FROM teachers t
             JOIN users u ON u.id = t.user_id
             ORDER BY u.name"
        )->fetchAll();

        $students = $db->query(
            "SELECT s.id, s.student_id, u.name, u.email
             FROM students s
             JOIN users u ON u.id = s.user_id
             ORDER BY u.name"
        )->fetchAll();

        // Recent notifications sent by admin
        $sent = $db->query(
            "SELECT n.*, u.name as recipient_name
             FROM notifications n
             JOIN users u ON u.id = n.recipient_id
             WHERE n.sender_role = 'admin'
             ORDER BY n.created_at DESC LIMIT 30"
        )->fetchAll();

        $this->view('admin/notifications', [
            'teachers' => $teachers,
            'students' => $students,
            'sent'     => $sent,
            'user'     => $admin,
        ], 'admin_layout');
    }

    public function send(): void {
        (new RoleMiddleware('admin'))->handle();

        $admin         = $this->currentUser();
        $message       = Request::sanitize(Request::post('message'));
        $target        = Request::post('target'); // 'all_students' | 'all_teachers' | 'all' | 'student_{id}' | 'teacher_{id}'

        if (!$message || !$target) {
            Session::flash('error', 'Message and target are required.');
            $this->redirect('admin/notifications');
        }

        $db       = Database::getInstance();
        $adminRow = $db->query(
            "SELECT id FROM admins WHERE user_id = " . (int)$admin['id']
        )->fetch();

        if (!$adminRow || !isset($adminRow['id'])) {
            Session::flash('error', 'Admin profile not found.');
            $this->redirect('admin/notifications');
        }

        $senderId = (int)$adminRow['id'];

        switch ($target) {
            case 'all_students':
                $users = $db->query("SELECT u.id FROM users u WHERE u.role = 'student'")->fetchAll();
                foreach ($users as $u) {
                    NotificationHelper::send($u['id'], $message, 'admin', $senderId, 'student');
                }
                break;

            case 'all_teachers':
                $users = $db->query("SELECT u.id FROM users u WHERE u.role = 'teacher'")->fetchAll();
                foreach ($users as $u) {
                    NotificationHelper::send($u['id'], $message, 'admin', $senderId, 'teacher');
                }
                break;

            case 'all':
                $users = $db->query("SELECT u.id, u.role FROM users u WHERE u.role IN ('student','teacher')")->fetchAll();
                foreach ($users as $u) {
                    NotificationHelper::send($u['id'], $message, 'admin', $senderId, $u['role']);
                }
                break;

            case 'student_direct':
                $studentIdentifier = trim((string)Request::post('student_identifier'));
                if (!$studentIdentifier) {
                    Session::flash('error', 'Student email or ID is required.');
                    $this->redirect('admin/notifications');
                }

                // Match by:
                // - users.email
                // - students.student_id (public student code)
                // - students.id (row id)
                // Use positional placeholders to avoid PDO named-placeholder issues.
                $stmt = $db->prepare(
                    "SELECT u.id AS user_id
                     FROM students s
                     JOIN users u ON u.id = s.user_id
                     WHERE u.email = ?
                        OR s.student_id = ?
                        OR s.id = ?
                     LIMIT 1"
                );
                $stmt->execute([$studentIdentifier, $studentIdentifier, $studentIdentifier]);
                $row = $stmt->fetch();

                if ($row) {
                    NotificationHelper::send((int)$row['user_id'], $message, 'admin', $senderId, 'student');
                } else {
                    Session::flash('error', 'Student not found for the given email/ID.');
                    $this->redirect('admin/notifications');
                }
                break;

            default:
                // Specific user: format 'student_5' or 'teacher_3'
                if (preg_match('/^(student|teacher)_(\d+)$/', $target, $m)) {
                    $role      = $m[1];
                    $profileId = (int)$m[2];
                    $col       = $role === 'student' ? 'students' : 'teachers';
                    $row       = $db->query("SELECT user_id FROM {$col} WHERE id = {$profileId}")->fetch();
                    if ($row) {
                        NotificationHelper::send($row['user_id'], $message, 'admin', $senderId, $role);
                    }
                }
        }

        Session::flash('success', 'Notification sent.');
        $this->redirect('admin/notifications');
    }
}

<?php

/**
 * Teacher notifications
 */
class NotificationController extends Controller {

    public function index(): void {
        (new RoleMiddleware('teacher'))->handle();

        $user        = $this->currentUser();
        $notifModel  = new NotificationModel();

        // Fetch all notifications received by the teacher
        $notifications = $notifModel->getForUser($user['id']);

        // Mark all as read on viewing
        $notifModel->markAllRead($user['id']);

        // To send messages, we need to know the teacher's students
        $db = Database::getInstance();
        $teacherModel = new TeacherModel();
        $teacher = $teacherModel->findByUserId($user['id']);
        
        $studentsStmt = $db->prepare(
            "SELECT DISTINCT u.id as user_id, u.name, s.student_id
             FROM enrollments e 
             JOIN courses c ON c.id = e.course_id 
             JOIN students s ON s.id = e.student_id 
             JOIN users u ON u.id = s.user_id 
             WHERE c.teacher_id = ?
             ORDER BY u.name"
        );
        $studentsStmt->execute([$teacher['id']]);
        $myStudents = $studentsStmt->fetchAll();

        // My Courses for filtering
        $coursesStmt = $db->prepare("SELECT id, name FROM courses WHERE teacher_id = ? ORDER BY name");
        $coursesStmt->execute([$teacher['id']]);
        $myCourses = $coursesStmt->fetchAll();

        $this->view('teacher/notifications', [
            'notifications' => $notifications,
            'myStudents'    => $myStudents,
            'myCourses'     => $myCourses,
            'user'          => $user,
        ], 'teacher_layout');
    }

    public function send(): void {
        (new RoleMiddleware('teacher'))->handle();

        $user    = $this->currentUser();
        $teacherModel = new TeacherModel();
        $teacher = $teacherModel->findByUserId($user['id']);

        $recipientType = Request::post('recipient_type'); // 'all', 'course', 'student'
        $courseId      = (int) Request::post('course_id');
        $studentUserId = (int) Request::post('student_user_id');
        $message       = Request::sanitize(Request::post('message'));

        if (!$message) {
            Session::flash('error', 'Message cannot be empty.');
            $this->redirect('teacher/notifications');
        }

        $db = Database::getInstance();
        $recipients = [];

        if ($recipientType === 'all') {
            $stmt = $db->prepare(
                "SELECT DISTINCT u.id 
                 FROM enrollments e 
                 JOIN courses c ON c.id = e.course_id 
                 JOIN students s ON s.id = e.student_id 
                 JOIN users u ON u.id = s.user_id 
                 WHERE c.teacher_id = ?"
            );
            $stmt->execute([$teacher['id']]);
            $recipients = $stmt->fetchAll(PDO::FETCH_COLUMN);

        } elseif ($recipientType === 'course') {
            if (!$courseId) {
                Session::flash('error', 'Select a course.');
                $this->redirect('teacher/notifications');
            }
            $stmt = $db->prepare(
                "SELECT DISTINCT u.id 
                 FROM enrollments e 
                 JOIN courses c ON c.id = e.course_id 
                 JOIN students s ON s.id = e.student_id 
                 JOIN users u ON u.id = s.user_id 
                 WHERE c.teacher_id = ? AND e.course_id = ?"
            );
            $stmt->execute([$teacher['id'], $courseId]);
            $recipients = $stmt->fetchAll(PDO::FETCH_COLUMN);

        } elseif ($recipientType === 'student') {
            if (!$studentUserId) {
                Session::flash('error', 'Select a student.');
                $this->redirect('teacher/notifications');
            }
            // Verify student belongs to this teacher
            $stmt = $db->prepare(
                "SELECT DISTINCT u.id 
                 FROM enrollments e 
                 JOIN courses c ON c.id = e.course_id 
                 JOIN students s ON s.id = e.student_id 
                 JOIN users u ON u.id = s.user_id 
                 WHERE c.teacher_id = ? AND u.id = ?"
            );
            $stmt->execute([$teacher['id'], $studentUserId]);
            if ($stmt->fetch()) {
                $recipients[] = $studentUserId;
            }
        }

        if (empty($recipients)) {
            Session::flash('error', 'No valid recipients found.');
            $this->redirect('teacher/notifications');
        }

        foreach ($recipients as $uid) {
            NotificationHelper::send($uid, $message, 'teacher', $teacher['id'], 'student');
        }

        Session::flash('success', 'Message sent to ' . count($recipients) . ' student(s).');
        $this->redirect('teacher/notifications');
    }
}

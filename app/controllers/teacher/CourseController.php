<?php

/**
 * View and update course details, send join links
 */
class CourseController extends Controller {

    private function getTeacher(): array {
        $user         = $this->currentUser();
        $teacherModel = new TeacherModel();
        $teacher      = $teacherModel->findByUserId($user['id']);
        if (!$teacher) $this->abort(403);
        return $teacher;
    }

    public function index(): void {
        (new RoleMiddleware('teacher'))->handle();

        $teacher = $this->getTeacher();
        $db      = Database::getInstance();

        // My Courses with student count
        $stmt = $db->prepare(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as student_count
             FROM courses c 
             WHERE c.teacher_id = ? 
             ORDER BY c.name"
        );
        $stmt->execute([$teacher['id']]);
        $courses = $stmt->fetchAll();

        $this->view('teacher/courses', [
            'courses' => $courses,
        ], 'teacher_layout');
    }

    public function show(string $id): void {
        (new RoleMiddleware('teacher'))->handle();

        $teacher  = $this->getTeacher();
        $courseId = (int) $id;
        $db       = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM courses WHERE id = ? AND teacher_id = ? LIMIT 1");
        $stmt->execute([$courseId, $teacher['id']]);
        $course = $stmt->fetch();

        if (!$course) {
            Session::flash('error', 'Course not found.');
            $this->redirect('teacher/courses');
        }

        // Enrolled Students
        $studentsStmt = $db->prepare(
            "SELECT s.id as student_id_pk, s.student_id, u.name, u.email, u.phone, e.enrolled_at 
             FROM enrollments e 
             JOIN students s ON s.id = e.student_id 
             JOIN users u ON u.id = s.user_id 
             WHERE e.course_id = ? 
             ORDER BY u.name"
        );
        $studentsStmt->execute([$courseId]);
        $students = $studentsStmt->fetchAll();

        $this->view('teacher/course_detail', [
            'course'   => $course,
            'students' => $students,
        ], 'teacher_layout');
    }

    public function update(string $id): void {
        (new RoleMiddleware('teacher'))->handle();

        $teacher  = $this->getTeacher();
        $courseId = (int) $id;
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM courses WHERE id = ? AND teacher_id = ? LIMIT 1");
        $stmt->execute([$courseId, $teacher['id']]);
        if (!$stmt->fetch()) {
            Session::flash('error', 'Course not found.');
            $this->redirect('teacher/courses');
        }

        $description = Request::sanitize(Request::post('description', ''));

        // --- Class Days (multi-checkbox) ---
        $rawDays   = $_POST['class_days'] ?? [];
        $validDays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $classDays = implode(',', array_filter($rawDays, fn($d) => in_array($d, $validDays)));

        // --- Class Start Time ---
        $startH    = (int) Request::post('start_hour', 12);
        $startM    = Request::post('start_min', '00');
        $startAmpm = Request::post('start_ampm', 'AM');
        if ($startAmpm === 'PM' && $startH !== 12) $startH += 12;
        if ($startAmpm === 'AM' && $startH === 12) $startH = 0;
        $classStartTime = sprintf('%02d:%s:00', $startH, $startM);

        // --- Class End Time ---
        $endH    = (int) Request::post('end_hour', 12);
        $endM    = Request::post('end_min', '00');
        $endAmpm = Request::post('end_ampm', 'AM');
        if ($endAmpm === 'PM' && $endH !== 12) $endH += 12;
        if ($endAmpm === 'AM' && $endH === 12) $endH = 0;
        $classEndTime = sprintf('%02d:%s:00', $endH, $endM);

        $coverImage = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['cover_image']['tmp_name'];
            $name = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES['cover_image']['name']);
            $dir = ROOT . '/public/uploads/courses';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            if (move_uploaded_file($tmp, "$dir/$name")) {
                $coverImage = $name;
            }
        }

        if ($coverImage) {
            $updateStmt = $db->prepare(
                "UPDATE courses SET description = ?, class_days = ?, class_start_time = ?, class_end_time = ?, cover_image = ? WHERE id = ?"
            );
            $updateStmt->execute([$description, $classDays ?: null, $classStartTime, $classEndTime, $coverImage, $courseId]);
        } else {
            $updateStmt = $db->prepare(
                "UPDATE courses SET description = ?, class_days = ?, class_start_time = ?, class_end_time = ? WHERE id = ?"
            );
            $updateStmt->execute([$description, $classDays ?: null, $classStartTime, $classEndTime, $courseId]);
        }

        Session::flash('success', 'Course updated successfully.');
        $this->redirect('teacher/courses/' . $courseId);

    }

    public function sendJoinLink(string $id): void {
        (new RoleMiddleware('teacher'))->handle();

        $teacher  = $this->getTeacher();
        $courseId = (int) $id;
        $link     = Request::post('join_link');

        if (!$link) {
            Session::flash('error', 'Join link is required.');
            $this->redirect('teacher/courses/' . $courseId);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, name FROM courses WHERE id = ? AND teacher_id = ? LIMIT 1");
        $stmt->execute([$courseId, $teacher['id']]);
        $course = $stmt->fetch();

        if (!$course) {
            Session::flash('error', 'Course not found.');
            $this->redirect('teacher/courses');
        }

        // Get all enrolled students
        $enrollModel = new EnrollmentModel();
        $enrolled    = $enrollModel->getByCourse($courseId);

        $sentCount = 0;
        $suspendedCount = 0;
        foreach ($enrolled as $e) {
            if (NotificationHelper::sendJoinLink((int)$e['student_id'], $courseId, $link, $teacher['id'])) {
                $sentCount++;
            } else {
                $suspendedCount++;
            }
        }

        if ($suspendedCount > 0) {
            NotificationHelper::notifyAdminSuspension($courseId, $suspendedCount);
        }

        $msg = "Join link sent to {$sentCount} students.";
        if ($suspendedCount > 0) {
            $msg .= " ({$suspendedCount} students skipped due to suspended access - Admins notified).";
        }
        Session::flash('success', $msg);
        $this->redirect('teacher/courses/' . $courseId);
    }
}

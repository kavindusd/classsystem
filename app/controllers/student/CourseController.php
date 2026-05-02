<?php

/**
 * Browse and search available courses, view details, and submit first slip to join.
 */
class CourseController extends Controller {

    private function getStudent(): array {
        $user         = $this->currentUser();
        $studentModel = new StudentModel();
        $student      = $studentModel->findByUserId($user['id']);
        if (!$student) $this->abort(403);
        return $student;
    }

    public function index(): void {
        (new RoleMiddleware('student'))->handle();

        $student     = $this->getStudent();
        $courseModel = new CourseModel();
        $enrollModel = new EnrollmentModel();

        // All active courses
        $courses     = $courseModel->getAllWithTeacher();

        // Get enrolled course IDs for this student
        $enrolled    = $enrollModel->getByStudent($student['id']);
        $enrolledIds = array_column($enrolled, 'course_id');

        $this->view('student/courses', [
            'courses'     => $courses,
            'enrolledIds' => $enrolledIds,
            'user'        => $this->currentUser(),
        ], 'student_layout');
    }

    public function search(): void {
        (new RoleMiddleware('student'))->handle();

        $student     = $this->getStudent();
        $keyword     = Request::get('q', '');
        $subject     = Request::get('subject', '');
        $grade       = Request::get('grade', '');

        $courseModel = new CourseModel();
        $enrollModel = new EnrollmentModel();

        $courses     = $courseModel->search($keyword, $subject, $grade);
        $enrolled    = $enrollModel->getByStudent($student['id']);
        $enrolledIds = array_column($enrolled, 'course_id');

        // Distinct subjects and grades for filter dropdowns
        $db       = Database::getInstance();
        $subjects = $db->query("SELECT DISTINCT subject FROM courses WHERE status='active' ORDER BY subject")->fetchAll(PDO::FETCH_COLUMN);
        $grades   = $db->query("SELECT DISTINCT grade FROM courses WHERE status='active' ORDER BY grade")->fetchAll(PDO::FETCH_COLUMN);

        $this->view('student/courses', [
            'courses'     => $courses,
            'enrolledIds' => $enrolledIds,
            'keyword'     => $keyword,
            'filter_subject' => $subject,
            'filter_grade'   => $grade,
            'subjects'    => $subjects,
            'grades'      => $grades,
            'is_search'   => true,
            'user'        => $this->currentUser(),
        ], 'student_layout');
    }

    public function show(string $id): void {
        (new RoleMiddleware('student'))->handle();

        $student     = $this->getStudent();
        $db          = Database::getInstance();

        // Course with teacher info
        $stmt = $db->prepare(
            "SELECT c.*, u.name as teacher_name, u.profile_image as teacher_image, t.teacher_id
             FROM courses c
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             WHERE c.id = ? AND c.status = 'active'
             LIMIT 1"
        );
        $stmt->execute([(int)$id]);
        $course = $stmt->fetch();

        if (!$course) {
            Session::flash('error', 'Course not found.');
            $this->redirect('student/courses');
        }

        // Is already enrolled?
        $enrollModel = new EnrollmentModel();
        $enrolled    = $enrollModel->findEnrollment($student['id'], (int)$id);

        // Existing slip for this month (to prevent duplicate)
        $slipModel   = new SlipModel();
        $currentMonth = date('Y-m');
        $existingSlip = $slipModel->getSlipForMonth($student['id'], (int)$id, $currentMonth);

        // Submission window open?
        $submissionOpen = SlipHelper::isSubmissionOpen();

        // All slips for this course
        $allSlips = $db->prepare(
            "SELECT * FROM slips WHERE student_id = ? AND course_id = ? ORDER BY slip_month DESC"
        );
        $allSlips->execute([$student['id'], (int)$id]);
        $slips = $allSlips->fetchAll();

        // Access status (for grace period)
        $hasAccess = $slipModel->hasAccess($student['id'], (int)$id);

        $this->view('student/course_detail', [
            'course'          => $course,
            'enrolled'        => $enrolled,
            'existingSlip'    => $existingSlip,
            'submissionOpen'  => $submissionOpen,
            'currentMonth'    => $currentMonth,
            'slips'           => $slips,
            'hasAccess'       => $hasAccess,
            'user'            => $this->currentUser(),
        ], 'student_layout');
    }
}

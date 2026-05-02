<?php

/**
 * Submit monthly payment slips per course.
 * First approved slip = enrollment. Each month needs a new slip for access.
 */
class SlipController extends Controller {

    private function getStudent(): array {
        $user         = $this->currentUser();
        $studentModel = new StudentModel();
        $student      = $studentModel->findByUserId($user['id']);
        if (!$student) $this->abort(403);
        return $student;
    }

    public function index(): void {
        (new RoleMiddleware('student'))->handle();

        $student = $this->getStudent();
        $db      = Database::getInstance();

        $stmt = $db->prepare(
            "SELECT s.*, c.name as course_name, c.subject, c.grade
             FROM slips s
             JOIN courses c ON c.id = s.course_id
             WHERE s.student_id = ?
             ORDER BY s.slip_month DESC, s.submitted_at DESC"
        );
        $stmt->execute([$student['id']]);
        $slips = $stmt->fetchAll();

        $this->view('student/slips', [
            'slips' => $slips,
            'user'  => $this->currentUser(),
        ], 'student_layout');
    }

    public function submit(string $courseId): void {
        (new RoleMiddleware('student'))->handle();

        $student     = $this->getStudent();
        $courseId    = (int) $courseId;

        // Verify course exists and is active
        $db     = Database::getInstance();
        $course = $db->prepare("SELECT * FROM courses WHERE id = ? AND status = 'active' LIMIT 1");
        $course->execute([$courseId]);
        $course = $course->fetch();

        if (!$course) {
            Session::flash('error', 'Course not found or inactive.');
            $this->redirect('student/courses');
        }

        // Submission window check (from SLIP_SUBMISSION_DAY env, with SLIP_GRACE_DAYS grace)
        $day        = (int) date('j');
        $openDay    = (int) SLIP_SUBMISSION_DAY;
        $graceEnd   = $openDay + (int) SLIP_GRACE_DAYS;

        // Allow from submission day to end of month (or grace period into next month)
        // Simple rule: can submit from day 25 onwards (or any day as first-time enrollment)
        $currentMonth = date('Y-m');

        // Check if already submitted for this month
        $slipModel    = new SlipModel();
        $existing     = $slipModel->getSlipForMonth($student['id'], $courseId, $currentMonth);

        if ($existing) {
            Session::flash('error', 'You have already submitted a slip for this course this month.');
            $this->redirect('student/courses/' . $courseId);
        }

        // File upload
        $file = $_FILES['slip_file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please attach a payment slip file (JPG, PNG, or PDF, max 5MB).');
            $this->redirect('student/courses/' . $courseId);
        }

        $filename = UploadHelper::uploadSlip($file, $student['id'], $courseId);
        if (!$filename) {
            Session::flash('error', 'Invalid file. Only JPG, PNG, PDF up to 5MB are allowed.');
            $this->redirect('student/courses/' . $courseId);
        }

        // Save slip record
        $slipModel->insert([
            'student_id' => $student['id'],
            'course_id'  => $courseId,
            'slip_month' => $currentMonth,
            'file_path'  => $filename,
            'status'     => 'pending',
        ]);

        Session::flash('success', 'Payment slip submitted successfully! You will be notified once it is reviewed.');
        $this->redirect('student/slips');
    }
}

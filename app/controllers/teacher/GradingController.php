<?php

/**
 * Create exam sheets and enter student grades
 */
class GradingController extends Controller {

    private function getTeacher(): array {
        $user         = $this->currentUser();
        $teacherModel = new TeacherModel();
        $teacher      = $teacherModel->findByUserId($user['id']);
        if (!$teacher) $this->abort(403);
        return $teacher;
    }

    private function gradingBase(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return str_contains($uri, '/teacher/exams') ? 'teacher/exams' : 'teacher/grading';
    }

    public function index(): void {
        (new RoleMiddleware('teacher'))->handle();

        $teacher = $this->getTeacher();
        $db      = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM courses WHERE teacher_id = ? ORDER BY name");
        $stmt->execute([$teacher['id']]);
        $courses = $stmt->fetchAll();

        $this->view('teacher/grading_courses', [
            'courses'      => $courses,
            'gradingBase'  => $this->gradingBase(),
        ], 'teacher_layout');
    }

    public function course(string $courseId): void {
        (new RoleMiddleware('teacher'))->handle();

        $teacher  = $this->getTeacher();
        $courseId = (int) $courseId;
        $db       = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM courses WHERE id = ? AND teacher_id = ? LIMIT 1");
        $stmt->execute([$courseId, $teacher['id']]);
        $course = $stmt->fetch();
        if (!$course) $this->abort(404);

        // Fetch enrolled students
        $studentsStmt = $db->prepare(
            "SELECT s.id as student_id_pk, s.student_id, u.name 
             FROM enrollments e 
             JOIN students s ON s.id = e.student_id 
             JOIN users u ON u.id = s.user_id 
             WHERE e.course_id = ? 
             ORDER BY u.name"
        );
        $studentsStmt->execute([$courseId]);
        $students = $studentsStmt->fetchAll();

        // Fetch exams
        $examModel = new ExamModel();
        $exams     = $examModel->getByCourse($courseId);

        // Fetch grades for the latest exam or selected exam
        $selectedExamId = (int) Request::get('exam_id', $exams[0]['id'] ?? 0);
        $grades = [];
        if ($selectedExamId) {
            $gradeModel = new GradeModel();
            $gradesRaw  = $gradeModel->getByExam($selectedExamId);
            foreach ($gradesRaw as $g) {
                $grades[$g['student_id']] = $g;
            }
        }

        $this->view('teacher/grading_course', [
            'course'         => $course,
            'students'       => $students,
            'exams'          => $exams,
            'selectedExamId' => $selectedExamId,
            'grades'         => $grades,
            'gradingBase'    => $this->gradingBase(),
        ], 'teacher_layout');
    }

    public function createExam(string $courseId): void {
        (new RoleMiddleware('teacher'))->handle();

        $teacher  = $this->getTeacher();
        $courseId = (int) $courseId;
        $title    = Request::post('title');

        if (!$title) {
            Session::flash('error', 'Exam title is required.');
            $this->redirect($this->gradingBase() . "/{$courseId}");
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM courses WHERE id = ? AND teacher_id = ? LIMIT 1");
        $stmt->execute([$courseId, $teacher['id']]);
        if (!$stmt->fetch()) $this->abort(404);

        $examModel = new ExamModel();
        $examModel->insert([
            'course_id'  => $courseId,
            'title'      => $title,
            'created_by' => $teacher['id']
        ]);

        Session::flash('success', 'Exam created.');
        $this->redirect($this->gradingBase() . "/{$courseId}");
    }

    public function saveGrades(string $courseId, string $examId): void {
        (new RoleMiddleware('teacher'))->handle();

        $teacher  = $this->getTeacher();
        $courseId = (int) $courseId;
        $examId   = (int) $examId;

        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT id FROM courses WHERE id = ? AND teacher_id = ? LIMIT 1");
        $stmt->execute([$courseId, $teacher['id']]);
        if (!$stmt->fetch()) $this->abort(404);

        $stmt = $db->prepare("SELECT id FROM exams WHERE id = ? AND course_id = ? LIMIT 1");
        $stmt->execute([$examId, $courseId]);
        if (!$stmt->fetch()) $this->abort(404);

        $grades  = Request::post('grades', []);
        $remarks = Request::post('remarks', []);

        $gradeModel = new GradeModel();
        
        foreach ($grades as $studentId => $gradeValue) {
            if ($gradeValue === '') continue; // Skip empty
            
            $remarkValue = $remarks[$studentId] ?? '';
            
            $existing = $gradeModel->getForStudent($examId, $studentId);
            if ($existing) {
                $gradeModel->update($existing['id'], [
                    'grade'   => $gradeValue,
                    'remarks' => $remarkValue
                ]);
            } else {
                $gradeModel->insert([
                    'exam_id'    => $examId,
                    'student_id' => $studentId,
                    'grade'      => $gradeValue,
                    'remarks'    => $remarkValue
                ]);
            }
        }

        $msg = "Grades saved successfully.";
        // Notify students about new grades
        foreach ($grades as $studentId => $gradeValue) {
            $studentModel = new StudentModel();
            $studentRow = $studentModel->findById((int)$studentId);
            if ($studentRow) {
                NotificationHelper::send(
                    (int)$studentRow['user_id'],
                    "🔔 *New Result Released*: Your grade for the exam '{$examId}' in your course has been updated. Check your portal for details.",
                    'teacher',
                    $teacher['id'],
                    'student'
                );
            }
        }
        
        Session::flash('success', $msg);
        $this->redirect($this->gradingBase() . "/{$courseId}?exam_id={$examId}");
    }
}

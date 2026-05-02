<?php

/**
 * Student grading — view own grades per enrolled course and exam.
 */
class GradingController extends Controller {

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
        $enrollModel = new EnrollmentModel();
        $enrolled    = $enrollModel->getByStudent($student['id']);

        // Shape into course-like records the view can use
        $enrolledCourses = array_map(function($e) {
            return [
                'id'           => $e['course_id'],
                'name'         => $e['course_name'],
                'subject'      => $e['subject'],
                'grade'        => $e['grade'],
                'teacher_name' => $e['teacher_name'],
            ];
        }, $enrolled);

        $this->view('student/grading', [
            'enrolledCourses' => $enrolledCourses,
            'student'         => $student,
            'user'            => $this->currentUser(),
        ], 'student_layout');
    }

    public function course(string $courseId): void {
        (new RoleMiddleware('student'))->handle();

        $student     = $this->getStudent();
        $courseId    = (int) $courseId;

        // Verify enrolled
        $enrollModel = new EnrollmentModel();
        if (!$enrollModel->findEnrollment($student['id'], $courseId)) {
            Session::flash('error', 'You are not enrolled in this course.');
            $this->redirect('student/grading');
        }

        $db = Database::getInstance();

        // Course info
        $stmt = $db->prepare(
            "SELECT c.*, u.name as teacher_name FROM courses c
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             WHERE c.id = ? LIMIT 1"
        );
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();

        // Exams for this course
        $examModel = new ExamModel();
        $exams     = $examModel->getByCourse($courseId);

        // Student's grades for each exam
        $gradeModel = new GradeModel();
        $grades     = [];
        foreach ($exams as $exam) {
            $grade = $gradeModel->getForStudent($exam['id'], $student['id']);
            $grades[$exam['id']] = $grade ?: null;
        }

        $this->view('student/course_grades', [
            'course'  => $course,
            'exams'   => $exams,
            'grades'  => $grades,
            'student' => $student,
            'user'    => $this->currentUser(),
        ], 'student_layout');
    }

    public function exam(string $courseId, string $examId): void {
        (new RoleMiddleware('student'))->handle();

        $student  = $this->getStudent();
        $courseId = (int) $courseId;
        $examId   = (int) $examId;

        $enrollModel = new EnrollmentModel();
        if (!$enrollModel->findEnrollment($student['id'], $courseId)) {
            Session::flash('error', 'You are not enrolled in this course.');
            $this->redirect('student/grading');
        }

        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM exams WHERE id = ? AND course_id = ? LIMIT 1");
        $stmt->execute([$examId, $courseId]);
        $exam = $stmt->fetch();
        if (!$exam) $this->abort(404);

        $gradeModel = new GradeModel();
        $grade      = $gradeModel->getForStudent($examId, $student['id']);
        
        $stmt = $db->prepare(
            "SELECT c.*, u.name as teacher_name FROM courses c
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             WHERE c.id = ? LIMIT 1"
        );
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();

        $this->view('student/exam_result', [
            'course'  => $course,
            'exam'    => $exam,
            'grade'   => $grade,
            'student' => $student,
            'user'    => $this->currentUser(),
        ], 'student_layout');
    }
}

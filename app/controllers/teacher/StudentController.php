<?php

class StudentController extends Controller {

    public function index(): void {
        (new RoleMiddleware('teacher'))->handle();

        $user         = $this->currentUser();
        $teacherModel = new TeacherModel();
        $teacher      = $teacherModel->findByUserId($user['id']);

        if (!$teacher) $this->abort(403);

        $db = Database::getInstance();
        
        // Fetch teacher's courses for the filter dropdown
        $coursesStmt = $db->prepare("SELECT id, name FROM courses WHERE teacher_id = ? ORDER BY name ASC");
        $coursesStmt->execute([$teacher['id']]);
        $courses = $coursesStmt->fetchAll();

        $selectedCourseId = Request::get('course_id');

        // Fetch all students enrolled in any course belonging to this teacher
        $sql = "
            SELECT DISTINCT 
                u.name, 
                u.email, 
                u.phone,
                u.profile_image,
                st.student_id,
                st.whatsapp_number,
                MIN(e.enrolled_at) as first_enrolled
            FROM enrollments e
            JOIN courses c ON c.id = e.course_id
            JOIN students st ON st.id = e.student_id
            JOIN users u ON u.id = st.user_id
            WHERE c.teacher_id = ?
        ";
        
        $params = [$teacher['id']];

        if (!empty($selectedCourseId)) {
            $sql .= " AND c.id = ? ";
            $params[] = (int)$selectedCourseId;
        }

        $sql .= " GROUP BY st.id ORDER BY u.name ASC ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        $this->view('teacher/students', [
            'students'       => $students,
            'courses'        => $courses,
            'selectedCourse' => $selectedCourseId
        ], 'teacher_layout');
    }
}

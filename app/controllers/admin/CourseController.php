<?php

class CourseController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $search  = Request::get('search', '');
        $subject = Request::get('subject', '');
        $grade   = Request::get('grade', '');
        $db      = Database::getInstance();

        $sql    = "SELECT c.*, t.teacher_id as teacher_display_id, u.name as teacher_name
                   FROM courses c
                   JOIN teachers t ON t.id = c.teacher_id
                   JOIN users u ON u.id = t.user_id
                   WHERE 1=1";
        $params = [];

        if ($search) {
            $sql    .= " AND (c.name LIKE ? OR c.subject LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($subject) {
            $sql    .= " AND c.subject = ?";
            $params[] = $subject;
        }
        if ($grade) {
            $sql    .= " AND c.grade = ?";
            $params[] = $grade;
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $courses = $stmt->fetchAll();

        // For teacher dropdown in create form
        $teachers = $db->query(
            "SELECT t.id, t.teacher_id, u.name
             FROM teachers t JOIN users u ON u.id = t.user_id
             ORDER BY u.name"
        )->fetchAll();

        $subjects = $db->query("SELECT DISTINCT subject FROM courses ORDER BY subject")->fetchAll(PDO::FETCH_COLUMN);
        $grades   = $db->query("SELECT DISTINCT grade FROM courses ORDER BY grade")->fetchAll(PDO::FETCH_COLUMN);

        $this->view('admin/courses', [
            'courses'  => $courses,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'grades'   => $grades,
            'search'   => $search,
            'filter_subject' => $subject,
            'filter_grade'   => $grade,
        ], 'admin_layout');
    }

    public function create(): void {
        (new RoleMiddleware('admin'))->handle();

        $teacherId   = Request::post('teacher_id');
        $name        = Request::sanitize(Request::post('name'));
        $subject     = Request::sanitize(Request::post('subject'));
        $grade       = Request::sanitize(Request::post('grade'));
        $price       = (float) Request::post('price');
        $instituteCost = (float) Request::post('institute_cost', 0);
        $teacherCommission = (float) Request::post('teacher_commission', 0);
        $description = Request::sanitize(Request::post('description'));
        $classDay    = Request::sanitize(Request::post('class_day'));
        $classTime   = Request::sanitize(Request::post('class_time'));

        if (!$teacherId || !$name || !$subject || !$grade) {
            Session::flash('error', 'All required fields must be filled.');
            $this->redirect('admin/courses');
        }

        $coverImage = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['cover_image']['tmp_name'];
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES['cover_image']['name']);
            $dir = ROOT . '/public/uploads/courses';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            if (move_uploaded_file($tmp, "$dir/$filename")) {
                $coverImage = $filename;
            }
        }

        $courseModel = new CourseModel();
        $courseModel->insert([
            'teacher_id'  => (int)$teacherId,
            'name'        => $name,
            'subject'     => $subject,
            'grade'       => $grade,
            'price'       => $price,
            'institute_cost'     => $instituteCost,
            'teacher_commission' => $teacherCommission,
            'description' => $description,
            'cover_image' => $coverImage,
            'status'      => 'active',
        ]);

        Session::flash('success', 'Course created successfully.');
        $this->redirect('admin/courses');
    }

    public function update(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $courseModel = new CourseModel();
        $course      = $courseModel->findById((int)$id);

        if (!$course) {
            Session::flash('error', 'Course not found.');
            $this->redirect('admin/courses');
        }

        $updateData = [
            'teacher_id'  => (int)Request::post('teacher_id'),
            'name'        => Request::sanitize(Request::post('name')),
            'subject'     => Request::sanitize(Request::post('subject')),
            'grade'       => Request::sanitize(Request::post('grade')),
            'price'       => (float)Request::post('price'),
            'institute_cost'     => (float)Request::post('institute_cost', 0),
            'teacher_commission' => (float)Request::post('teacher_commission', 0),
            'description' => Request::sanitize(Request::post('description')),
            'status'      => Request::post('status', 'active'),
        ];

        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['cover_image']['tmp_name'];
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES['cover_image']['name']);
            $dir = ROOT . '/public/uploads/courses';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            if (move_uploaded_file($tmp, "$dir/$filename")) {
                $updateData['cover_image'] = $filename;
            }
        }

        $courseModel->update((int)$id, $updateData);

        Session::flash('success', 'Course updated.');
        $this->redirect('admin/courses');
    }

    public function delete(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $courseModel = new CourseModel();
        $courseModel->delete((int)$id);

        Session::flash('success', 'Course deleted.');
        $this->redirect('admin/courses');
    }
}
<?php

class StudentController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $search = Request::get('search', '');
        $db     = Database::getInstance();

        $sql    = "SELECT s.*, u.name, u.email, u.phone, u.profile_image
                   FROM students s
                   JOIN users u ON u.id = s.user_id";
        $params = [];

        if ($search) {
            $sql   .= " WHERE u.name LIKE ? OR u.email LIKE ? OR s.student_id LIKE ? OR u.phone LIKE ?";
            $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
        }

        $sql .= " ORDER BY s.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        $this->view('admin/students', [
            'students' => $students,
            'search'   => $search,
        ], 'admin_layout');
    }

    public function create(): void {
        (new RoleMiddleware('admin'))->handle();

        $name     = Request::sanitize(Request::post('name'));
        $email    = Request::post('email');
        $phone    = Request::post('phone');
        $whatsapp = Request::post('whatsapp_number');
        $password = Request::post('password');

        if (!$name || (!$email && !$phone) || !$password) {
            Session::flash('error', 'Name, at least one contact (email or phone), and a password are required.');
            $this->redirect('admin/students');
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Password must be at least 8 characters.');
            $this->redirect('admin/students');
        }

        $userModel = new UserModel();
        if ($email && $userModel->findByEmail($email)) {
            Session::flash('error', 'This email is already in use.');
            $this->redirect('admin/students');
        }
        if ($phone && $userModel->findByPhone($phone)) {
            Session::flash('error', 'This phone number is already in use.');
            $this->redirect('admin/students');
        }

        $studentId = AuthHelper::generateStudentId();
        $userId    = $userModel->createUser($name, $email ?: null, $phone ?: null, $password, 'student');

        $studentModel = new StudentModel();
        $studentModel->insert([
            'user_id'         => $userId,
            'student_id'      => $studentId,
            'whatsapp_number' => $whatsapp ?: null,
            'whatsapp_enabled'=> $whatsapp ? 1 : 0
        ]);

        if ($email) {
            MailHelper::sendStudentId($email, $studentId, $name);
        } else {
            error_log("[STUDENT_ID] Phone: {$phone} ID: {$studentId}");
        }

        Session::flash('success', "Student created. ID: {$studentId}");
        $this->redirect('admin/students');
    }

    public function update(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $studentModel = new StudentModel();
        $student      = $studentModel->findById((int)$id);

        if (!$student) {
            Session::flash('error', 'Student not found.');
            $this->redirect('admin/students');
        }

        $name  = Request::sanitize(Request::post('name'));
        $email = Request::post('email');
        $phone = Request::post('phone');
        $whatsapp = Request::post('whatsapp_number');

        $userModel = new UserModel();
        $userModel->update($student['user_id'], [
            'name'  => $name,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
        ]);

        $studentModel->update($student['id'], [
            'whatsapp_number' => $whatsapp ?: null,
            'whatsapp_enabled'=> $whatsapp ? 1 : 0
        ]);

        Session::flash('success', 'Student updated.');
        $this->redirect('admin/students');
    }

    public function delete(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $studentModel = new StudentModel();
        $student      = $studentModel->findById((int)$id);

        if (!$student) {
            Session::flash('error', 'Student not found.');
            $this->redirect('admin/students');
        }

        $userModel = new UserModel();
        $userModel->delete($student['user_id']);

        Session::flash('success', 'Student deleted.');
        $this->redirect('admin/students');
    }
}
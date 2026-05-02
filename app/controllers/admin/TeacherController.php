<?php

class TeacherController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $search = Request::get('search', '');
        $db     = Database::getInstance();

        $sql    = "SELECT t.*, u.name, u.email, u.phone, u.profile_image
                   FROM teachers t
                   JOIN users u ON u.id = t.user_id";
        $params = [];

        if ($search) {
            $sql   .= " WHERE u.name LIKE ? OR u.email LIKE ? OR t.teacher_id LIKE ?";
            $params = ["%$search%", "%$search%", "%$search%"];
        }

        $sql .= " ORDER BY t.created_at DESC";

        $teachers = $db->prepare($sql);
        $teachers->execute($params);
        $teachers = $teachers->fetchAll();

        $this->view('admin/teachers', [
            'teachers' => $teachers,
            'search'   => $search,
        ], 'admin_layout');
    }

    public function create(): void {
        (new RoleMiddleware('admin'))->handle();

        $name    = Request::sanitize(Request::post('name'));
        $email   = Request::post('email');
        $phone   = Request::post('phone');

        if (!$name || (!$email && !$phone)) {
            Session::flash('error', 'Name and at least one contact (email or phone) are required.');
            $this->redirect('admin/teachers');
        }

        // Check duplicates
        $userModel = new UserModel();
        if ($email && $userModel->findByEmail($email)) {
            Session::flash('error', 'This email is already in use.');
            $this->redirect('admin/teachers');
        }
        if ($phone && $userModel->findByPhone($phone)) {
            Session::flash('error', 'This phone number is already in use.');
            $this->redirect('admin/teachers');
        }

        // Handle profile image upload
        $profileImage = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['profile_image']['tmp_name'];
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES['profile_image']['name']);
            $dir = ROOT . '/public/uploads/profiles';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            if (move_uploaded_file($tmp, "$dir/$filename")) {
                $profileImage = $filename;
            }
        }

        // Generate credentials
        $teacherId   = AuthHelper::generateTeacherId();
        $tempPassword = AuthHelper::generateOneTimePassword();

        // Create user + teacher record
        $userId = $userModel->createUser($name, $email ?: null, $phone ?: null, $tempPassword, 'teacher', $profileImage);

        $teacherModel = new TeacherModel();
        $teacherModel->insert([
            'user_id'        => $userId,
            'teacher_id'     => $teacherId,
            'is_first_login' => 1,
        ]);

        // Send credentials via email or phone
        if ($email) {
            MailHelper::sendTeacherCredentials($email, $teacherId, $tempPassword, $name);
        } else {
            // TODO: SMS gateway
            error_log("[TEACHER_CREDENTIALS] Phone: {$phone} ID: {$teacherId} Pass: {$tempPassword}");
        }

        Session::flash('success', "Teacher created. ID: {$teacherId} has been sent to their contact.");
        $this->redirect('admin/teachers');
    }

    public function update(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $teacherModel = new TeacherModel();
        $teacher      = $teacherModel->findById((int)$id);

        if (!$teacher) {
            Session::flash('error', 'Teacher not found.');
            $this->redirect('admin/teachers');
        }

        $name  = Request::sanitize(Request::post('name'));
        $email = Request::post('email');
        $phone = Request::post('phone');

        $updateData = [
            'name'  => $name,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
        ];

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['profile_image']['tmp_name'];
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES['profile_image']['name']);
            $dir = ROOT . '/public/uploads/profiles';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            if (move_uploaded_file($tmp, "$dir/$filename")) {
                $updateData['profile_image'] = $filename;
            }
        }

        $userModel = new UserModel();
        $userModel->update($teacher['user_id'], $updateData);

        Session::flash('success', 'Teacher updated successfully.');
        $this->redirect('admin/teachers');
    }

    public function delete(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $teacherModel = new TeacherModel();
        $teacher      = $teacherModel->findById((int)$id);

        if (!$teacher) {
            Session::flash('error', 'Teacher not found.');
            $this->redirect('admin/teachers');
        }

        // Deleting user cascades to teacher record
        $userModel = new UserModel();
        $userModel->delete($teacher['user_id']);

        Session::flash('success', 'Teacher deleted.');
        $this->redirect('admin/teachers');
    }
}
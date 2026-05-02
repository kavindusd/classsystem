<?php

/**
 * Update email and password with OTP verification
 */
class SettingsController extends Controller {

    public function index(): void {
        (new RoleMiddleware('teacher'))->handle();

        $user         = $this->currentUser();
        $teacherModel = new TeacherModel();
        $teacher      = $teacherModel->findByUserId($user['id']);
        if (!$teacher) $this->abort(403);

        $this->view('teacher/settings', [
            'user'    => $user,
            'teacher' => $teacher,
        ], 'teacher_layout');
    }

    public function update(): void {
        (new RoleMiddleware('teacher'))->handle();

        $user  = $this->currentUser();
        $name  = Request::sanitize(Request::post('name'));
        $email = Request::post('email');
        $phone = Request::post('phone');

        if (!$name) {
            Session::flash('error', 'Name cannot be empty.');
            $this->redirect('teacher/settings');
        }

        $userModel = new UserModel();

        if ($email && $email !== $user['email']) {
            if ($userModel->findByEmail($email)) {
                Session::flash('error', 'This email is already in use.');
                $this->redirect('teacher/settings');
            }
        }
        if ($phone && $phone !== $user['phone']) {
            if ($userModel->findByPhone($phone)) {
                Session::flash('error', 'This phone number is already in use.');
                $this->redirect('teacher/settings');
            }
        }

        $profileImage = $user['profile_image'] ?? null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['profile_image']['tmp_name'];
            $name = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES['profile_image']['name']);
            $dir = ROOT . '/public/uploads/profiles';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            if (move_uploaded_file($tmp, "$dir/$name")) {
                $profileImage = $name;
            }
        }

        $userModel->update($user['id'], [
            'name'  => $name,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
            'profile_image' => $profileImage
        ]);

        $updated = $userModel->findById($user['id']);
        Session::set('user', $updated);

        Session::flash('success', 'Profile updated successfully.');
        $this->redirect('teacher/settings');
    }

    public function changePassword(): void {
        (new RoleMiddleware('teacher'))->handle();

        $user       = $this->currentUser();
        $current    = Request::post('current_password');
        $password   = Request::post('password');
        $confirm    = Request::post('confirm_password');

        $userModel  = new UserModel();
        $fullUser   = $userModel->findById($user['id']);

        if (!AuthHelper::verifyPassword($current, $fullUser['password'])) {
            Session::flash('error', 'Current password is incorrect.');
            $this->redirect('teacher/settings');
        }

        if ($password !== $confirm) {
            Session::flash('error', 'New passwords do not match.');
            $this->redirect('teacher/settings');
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Password must be at least 8 characters.');
            $this->redirect('teacher/settings');
        }

        $userModel->updatePassword($user['id'], $password);

        // Mark first login as done if applicable
        $teacherModel = new TeacherModel();
        $teacher = $teacherModel->findByUserId($user['id']);
        if ($teacher && $teacher['is_first_login']) {
            $teacherModel->markFirstLoginDone($teacher['id']);
        }

        Session::flash('success', 'Password changed successfully.');
        $this->redirect('teacher/settings');
    }
}

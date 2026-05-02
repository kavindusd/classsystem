<?php

/**
 * Student profile settings — update name/contact and change password.
 */
class SettingsController extends Controller {

    private function getStudent(): array {
        $user         = $this->currentUser();
        $studentModel = new StudentModel();
        $student      = $studentModel->findByUserId($user['id']);
        if (!$student) $this->abort(403);
        return $student;
    }

    public function index(): void {
        (new RoleMiddleware('student'))->handle();

        $user    = $this->currentUser();
        $student = $this->getStudent();

        $this->view('student/settings', [
            'user'    => $user,
            'student' => $student,
        ], 'student_layout');
    }

    public function update(): void {
        (new RoleMiddleware('student'))->handle();

        $user  = $this->currentUser();
        $name  = Request::sanitize(Request::post('name'));
        $email = Request::post('email');
        $phone = Request::post('phone');
        $whatsapp = Request::post('whatsapp_number');

        if (!$name) {
            Session::flash('error', 'Name cannot be empty.');
            $this->redirect('student/settings');
        }

        $userModel = new UserModel();

        // Check uniqueness if email/phone changed
        if ($email && $email !== $user['email']) {
            if ($userModel->findByEmail($email)) {
                Session::flash('error', 'This email is already in use.');
                $this->redirect('student/settings');
            }
        }
        if ($phone && $phone !== $user['phone']) {
            if ($userModel->findByPhone($phone)) {
                Session::flash('error', 'This phone number is already in use.');
                $this->redirect('student/settings');
            }
        }

        $userModel->update($user['id'], [
            'name'  => $name,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
        ]);

        $student      = $this->getStudent();
        $studentModel = new StudentModel();
        $studentModel->update($student['id'], [
            'whatsapp_number' => $whatsapp ?: null,
            'whatsapp_enabled'=> $whatsapp ? 1 : 0
        ]);

        // Refresh session user
        $updated = $userModel->findById($user['id']);
        Session::set('user', $updated);

        Session::flash('success', 'Profile updated successfully.');
        $this->redirect('student/settings');
    }

    public function changePassword(): void {
        (new RoleMiddleware('student'))->handle();

        $user       = $this->currentUser();
        $current    = Request::post('current_password');
        $password   = Request::post('password');
        $confirm    = Request::post('confirm_password');

        $userModel  = new UserModel();
        $fullUser   = $userModel->findById($user['id']);

        if (!AuthHelper::verifyPassword($current, $fullUser['password'])) {
            Session::flash('error', 'Current password is incorrect.');
            $this->redirect('student/settings');
        }

        if ($password !== $confirm) {
            Session::flash('error', 'New passwords do not match.');
            $this->redirect('student/settings');
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Password must be at least 8 characters.');
            $this->redirect('student/settings');
        }

        $userModel->updatePassword($user['id'], $password);

        Session::flash('success', 'Password changed successfully.');
        $this->redirect('student/settings');
    }
}

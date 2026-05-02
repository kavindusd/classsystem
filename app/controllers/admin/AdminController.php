<?php

class AdminController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $db     = Database::getInstance();
        $admins = $db->query(
            "SELECT a.*, u.name, u.email FROM admins a JOIN users u ON u.id = a.user_id ORDER BY a.created_at"
        )->fetchAll();

        $this->view('admin/admins', ['admins' => $admins], 'admin_layout');
    }

    public function create(): void {
        (new RoleMiddleware('admin'))->handle();

        $name     = Request::sanitize(Request::post('name'));
        $email    = Request::post('email');
        $password = Request::post('password');

        if (!$name || !$email || !$password) {
            Session::flash('error', 'All fields are required.');
            $this->redirect('admin/admins');
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Password must be at least 8 characters.');
            $this->redirect('admin/admins');
        }

        $userModel = new UserModel();
        if ($userModel->findByEmail($email)) {
            Session::flash('error', 'Email already in use.');
            $this->redirect('admin/admins');
        }

        $db      = Database::getInstance();
        $count   = $db->query("SELECT COUNT(*) FROM admins")->fetchColumn();
        $adminId = 'ADM' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);

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

        $userId = $userModel->createUser($name, $email, null, $password, 'admin', $profileImage);

        $adminModel = new AdminModel();
        $adminModel->insert([
            'user_id'  => $userId,
            'admin_id' => $adminId,
        ]);

        Session::flash('success', 'Admin account created.');
        $this->redirect('admin/admins');
    }

    public function delete(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $current  = $this->currentUser();
        $adminModel = new AdminModel();
        $target   = $adminModel->findById((int)$id);

        if (!$target) {
            Session::flash('error', 'Admin not found.');
            $this->redirect('admin/admins');
        }

        // Prevent self-deletion
        if ($target['user_id'] == $current['id']) {
            Session::flash('error', 'You cannot delete your own account.');
            $this->redirect('admin/admins');
        }

        $userModel = new UserModel();
        $userModel->delete($target['user_id']);

        Session::flash('success', 'Admin account deleted.');
        $this->redirect('admin/admins');
    }

    public function changePassword(): void {
        (new RoleMiddleware('admin'))->handle();

        $current     = $this->currentUser();
        $step        = Request::post('step', 'request'); // 'request' or 'confirm'

        if ($step === 'request') {
            // Send OTP to admin email
            OtpHelper::sendTo($current['email'], 'password_reset');
            Session::set('pwd_change_identifier', $current['email']);
            Session::flash('success', 'Verification code sent to your email.');
            $this->redirect('admin/admins');
        }

        if ($step === 'confirm') {
            $otp      = trim(Request::post('otp'));
            $password = Request::post('password');
            $confirm  = Request::post('confirm_password');
            $identifier = Session::get('pwd_change_identifier');

            if (!OtpHelper::verify($identifier, $otp, 'password_reset')) {
                Session::flash('error', 'Invalid or expired code.');
                $this->redirect('admin/admins');
            }

            if ($password !== $confirm || strlen($password) < 8) {
                Session::flash('error', 'Passwords do not match or too short.');
                $this->redirect('admin/admins');
            }

            OtpHelper::invalidate($identifier, 'password_reset');
            $userModel = new UserModel();
            $userModel->updatePassword($current['id'], $password);

            Session::delete('pwd_change_identifier');
            Session::flash('success', 'Password updated successfully.');
            $this->redirect('admin/admins');
        }
    }
}
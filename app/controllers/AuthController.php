<?php

class AuthController extends Controller {

    // ─────────────────────────────────────────────
    // LOGIN
    // ─────────────────────────────────────────────

    public function showLogin(): void {
        if (Session::has('role')) {
            $this->redirectToPanel();
        }
        $this->view('auth/login', [], 'auth_layout');
    }

    public function login(): void {
        $identifier = trim(Request::post('identifier'));
        $password   = Request::post('password');

        if (!$identifier || !$password) {
            Session::flash('error', 'Please enter your ID and password.');
            $this->redirect('login');
        }

        error_log("[DEBUG] Login attempt for identifier: " . $identifier);
        $hintedRole = AuthHelper::detectRoleFromIdentifier($identifier);
        error_log("[DEBUG] Hinted role: " . ($hintedRole ?? 'none'));

        // ── Student login (Student ID)
        if ($hintedRole === 'student') {
            $studentModel = new StudentModel();
            $record       = $studentModel->findByStudentId(strtoupper($identifier));
            error_log("[DEBUG] Student lookup for " . strtoupper($identifier) . ": " . ($record ? 'found' : 'not found'));

            if (!$record || !AuthHelper::verifyPassword($password, $record['password'])) {
                Session::flash('error', 'Invalid Student ID or password.');
                $this->redirect('login');
            }

            AuthHelper::loginUser($record, 'student');
            $this->redirect('student');
        }

        // ── Teacher login (Teacher ID)
        if ($hintedRole === 'teacher') {
            $teacherModel = new TeacherModel();
            $record       = $teacherModel->findByTeacherId(strtoupper($identifier));
            error_log("[DEBUG] Teacher lookup for " . strtoupper($identifier) . ": " . ($record ? 'found' : 'not found'));

            if (!$record || !AuthHelper::verifyPassword($password, $record['password'])) {
                Session::flash('error', 'Invalid Teacher ID or password.');
                $this->redirect('login');
            }

            AuthHelper::loginUser($record, 'teacher');

            // Force password change on first login
            if (isset($record['is_first_login']) && $record['is_first_login']) {
                Session::set('force_password_change', true);
                $this->redirect('teacher/settings');
            }

            $this->redirect('teacher');
        }

        // ── Admin or Student login (email or phone)
        $userModel = new UserModel();
        
        // Check if phone login is allowed
        if (AuthHelper::isPhone($identifier)) {
            $settingModel = new SiteSettingModel();
            if ($settingModel->getSetting('phone_login_enabled', '0') !== '1') {
                Session::flash('error', 'Phone number authentication is disabled. Please use your email or ID.');
                $this->redirect('login');
            }
        }

        $user = $userModel->findByIdentifier($identifier);
        error_log("[DEBUG] User lookup by identifier for " . $identifier . ": " . ($user ? 'found (role: '.$user['role'].')' : 'not found'));

        if (!$user || !AuthHelper::verifyPassword($password, $user['password'])) {
            Session::flash('error', 'Invalid credentials.');
            $this->redirect('login');
        }

        AuthHelper::loginUser($user, $user['role']);
        $this->redirectToPanel();
    }

    // ─────────────────────────────────────────────
    // REGISTER (Students only)
    // ─────────────────────────────────────────────

    public function showRegister(): void {
        $this->view('auth/register', [], 'auth_layout');
    }

    public function register(): void {
        $name     = Request::sanitize(Request::post('name'));
        $contact  = Request::post('contact'); // email or phone
        $password = Request::post('password');
        $confirm  = Request::post('confirm_password');

        // Validation
        if (!$name || !$contact || !$password) {
            Session::flash('error', 'All fields are required.');
            $this->redirect('register');
        }

        if ($password !== $confirm) {
            Session::flash('error', 'Passwords do not match.');
            $this->redirect('register');
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Password must be at least 8 characters.');
            $this->redirect('register');
        }

        // Check if email/phone already exists
        $userModel = new UserModel();
        if (AuthHelper::isEmail($contact) && $userModel->findByEmail($contact)) {
            Session::flash('error', 'This email is already registered.');
            $this->redirect('register');
        }
        if (AuthHelper::isPhone($contact)) {
            $contact = AuthHelper::normalizePhone($contact);
            if ($userModel->findByPhone($contact)) {
                Session::flash('error', 'This phone number is already registered.');
                $this->redirect('register');
            }
        }

        // Store pending registration in session, send OTP
        Session::set('pending_registration', [
            'name'     => $name,
            'contact'  => $contact,
            'password' => $password,
        ]);

        OtpHelper::sendTo($contact, 'registration');

        Session::flash('success', 'Verification code sent. Please check your email or phone.');
        $this->redirect('verify-otp?purpose=registration');
    }

    // ─────────────────────────────────────────────
    // OTP VERIFICATION
    // ─────────────────────────────────────────────

    public function showVerifyOtp(): void {
        $purpose = Request::get('purpose', 'registration');
        $this->view('auth/verify_otp', ['purpose' => $purpose], 'auth_layout');
    }

    public function verifyOtp(): void {
        $code    = trim(Request::post('otp'));
        $purpose = Request::post('purpose');

        switch ($purpose) {

            case 'registration':
                $pending = Session::get('pending_registration');
                if (!$pending) {
                    $this->redirect('register');
                }

                if (!OtpHelper::verify($pending['contact'], $code, 'registration')) {
                    Session::flash('error', 'Invalid or expired code. Please try again.');
                    $this->redirect('verify-otp?purpose=registration');
                }

                OtpHelper::invalidate($pending['contact'], 'registration');

                // Create user + student record
                $userModel    = new UserModel();
                $studentModel = new StudentModel();

                $isEmail   = AuthHelper::isEmail($pending['contact']);
                $userId    = $userModel->createUser(
                    $pending['name'],
                    $isEmail ? $pending['contact'] : null,
                    $isEmail ? null : $pending['contact'],
                    $pending['password'],
                    'student'
                );

                $studentId = AuthHelper::generateStudentId();
                $studentModel->createStudent($userId, $studentId);

                // Send student ID via email or phone
                if ($isEmail) {
                    MailHelper::sendStudentId($pending['contact'], $studentId, $pending['name']);
                } else {
                    // TODO: SMS gateway — send student ID to phone
                    error_log("[STUDENT_ID] Phone: {$pending['contact']} ID: {$studentId}");
                }

                Session::delete('pending_registration');
                Session::flash('success', "Registration complete! Your Student ID is: {$studentId}. Use it to log in.");
                $this->redirect('login');
                break;

            case 'password_reset':
                $identifier = Session::get('reset_identifier');
                if (!$identifier) {
                    $this->redirect('forgot-password');
                }

                if (!OtpHelper::verify($identifier, $code, 'password_reset')) {
                    Session::flash('error', 'Invalid or expired code.');
                    $this->redirect('verify-otp?purpose=password_reset');
                }

                OtpHelper::invalidate($identifier, 'password_reset');
                Session::set('reset_verified', true);
                $this->redirect('reset-password');
                break;

            default:
                $this->redirect('login');
        }
    }

    // ─────────────────────────────────────────────
    // FORGOT PASSWORD
    // ─────────────────────────────────────────────

    public function showForgotPassword(): void {
        $this->view('auth/forgot_password', [], 'auth_layout');
    }

    public function forgotPassword(): void {
        $identifier = Request::post('identifier'); // email or phone

        if (!$identifier) {
            Session::flash('error', 'Please enter your email or phone number.');
            $this->redirect('forgot-password');
        }

        $userModel = new UserModel();
        $user      = $userModel->findByIdentifier($identifier);

        // Don't reveal if user exists — always show success message
        if ($user) {
            OtpHelper::sendTo($identifier, 'password_reset');
            Session::set('reset_identifier', $identifier);
        }

        Session::flash('success', 'If an account exists, a reset code has been sent.');
        $this->redirect('verify-otp?purpose=password_reset');
    }

    // ─────────────────────────────────────────────
    // RESET PASSWORD
    // ─────────────────────────────────────────────

    public function showResetPassword(): void {
        if (!Session::get('reset_verified')) {
            $this->redirect('forgot-password');
        }
        $this->view('auth/reset_password', [], 'auth_layout');
    }

    public function resetPassword(): void {
        if (!Session::get('reset_verified')) {
            $this->redirect('forgot-password');
        }

        $password = Request::post('password');
        $confirm  = Request::post('confirm_password');

        if ($password !== $confirm) {
            Session::flash('error', 'Passwords do not match.');
            $this->redirect('reset-password');
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Password must be at least 8 characters.');
            $this->redirect('reset-password');
        }

        $identifier = Session::get('reset_identifier');
        $userModel  = new UserModel();
        $user       = $userModel->findByIdentifier($identifier);

        if ($user) {
            $userModel->updatePassword($user['id'], $password);
        }

        Session::delete('reset_identifier');
        Session::delete('reset_verified');
        Session::flash('success', 'Password updated. Please log in.');
        $this->redirect('login');
    }

    // ─────────────────────────────────────────────
    // LOGOUT
    // ─────────────────────────────────────────────

    public function logout(): void {
        Session::destroy();
        $this->redirect('login');
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    private function redirectToPanel(): void {
        match(Session::get('role')) {
            'admin'   => $this->redirect('admin'),
            'teacher' => $this->redirect('teacher'),
            'student' => $this->redirect('student'),
            default   => $this->redirect('login'),
        };
    }
}
<?php

class AuthHelper {

    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public static function generateStudentId(): string {
        return STUDENT_ID_PREFIX . strtoupper(substr(uniqid(), -6));
    }

    public static function generateTeacherId(): string {
        return TEACHER_ID_PREFIX . strtoupper(substr(uniqid(), -6));
    }

    public static function generateOneTimePassword(int $length = 10): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $pass  = '';
        for ($i = 0; $i < $length; $i++) {
            $pass .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $pass;
    }

    public static function loginUser(array $user, string $role): void {
        // session_regenerate_id(true);
        Session::set('user', $user);
        Session::set('role', $role);
    }

    /**
     * Detect role from login identifier format.
     * STU*** = student, TCH*** = teacher, else try email/phone = admin or student
     */
    public static function detectRoleFromIdentifier(string $identifier): string|null {
        $identifier = strtoupper(trim($identifier));
        
        // If it looks like an email, don't hint student/teacher roles by prefix
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        if (str_starts_with($identifier, STUDENT_ID_PREFIX)) return 'student';
        if (str_starts_with($identifier, TEACHER_ID_PREFIX)) return 'teacher';
        return null; // email/phone — could be admin or student, check DB
    }

    public static function isEmail(string $value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isPhone(string $value): bool {
        return preg_match('/^\+?[0-9]{7,15}$/', $value) === 1;
    }

    /**
     * Normalize Sri Lankan phone numbers to 94 format.
     * 0771234567 -> 94771234567
     * 771234567 -> 94771234567
     * +94771234567 -> 94771234567
     */
    public static function normalizePhone(string $phone): string {
        $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-digits
        
        if (str_starts_with($phone, '0')) {
            $phone = '94' . substr($phone, 1);
        } elseif (strlen($phone) === 9 && !str_starts_with($phone, '94')) {
            $phone = '94' . $phone;
        }

        return $phone;
    }
}
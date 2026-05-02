<?php

class OtpHelper {

    public static function generate(): string {
        return (string) random_int(100000, 999999); // 6-digit
    }

    /**
     * Generate, store, and send OTP to email or phone.
     * Purpose: registration | password_reset | email_change | phone_change
     */
    public static function sendTo(string $identifier, string $purpose): bool {
        $code = self::generate();

        $otpModel = new OtpModel();
        $otpModel->store($identifier, $code, $purpose);

        if (AuthHelper::isEmail($identifier)) {
            return MailHelper::sendOtp($identifier, $code, $purpose);
        }

        if (AuthHelper::isPhone($identifier)) {
            return SmsHelper::sendOtp($identifier, $code);
        }

        return false;
    }

    public static function verify(string $identifier, string $code, string $purpose): bool {
        $otpModel = new OtpModel();
        return $otpModel->verify($identifier, $code, $purpose);
    }

    public static function invalidate(string $identifier, string $purpose): void {
        $otpModel = new OtpModel();
        $otpModel->invalidate($identifier, $purpose);
    }
}
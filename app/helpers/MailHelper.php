<?php

/**
 * MailHelper — sends emails via custom SMTP Socket implementation.
 * No external libraries (like PHPMailer) required.
 */
class MailHelper {

    public static function sendOtp(string $toEmail, string $code, string $purpose): bool {
        $purposeLabels = [
            'registration'   => 'Email Verification',
            'password_reset' => 'Password Reset',
            'email_change'   => 'Email Change Verification',
            'phone_change'   => 'Phone Change Verification',
        ];

        $label   = $purposeLabels[$purpose] ?? 'Verification';
        $appName = APP_NAME;
        $expiry  = OTP_EXPIRY_MINUTES;

        $subject = "{$appName} — {$label} Code";
        $body    = "Your {$label} code is: {$code}\n\nThis code expires in {$expiry} minutes.\nDo not share this code with anyone.";

        return self::send($toEmail, $subject, $body);
    }

    public static function sendStudentId(string $toEmail, string $studentId, string $name): bool {
        $appName = APP_NAME;
        $subject = "{$appName} — Your Student ID";
        $body    = "Hi {$name},\n\nYour registration is complete.\nYour Student ID is: {$studentId}\n\nUse this ID to log in to {$appName}.";
        return self::send($toEmail, $subject, $body);
    }

    public static function sendTeacherCredentials(string $toEmail, string $teacherId, string $tempPassword, string $name): bool {
        $appName = APP_NAME;
        $subject = "{$appName} — Your Teacher Account";
        $body    = "Hi {$name},\n\nYour teacher account has been created.\n\nTeacher ID : {$teacherId}\nPassword   : {$tempPassword}\n\nYou will be asked to change your password on first login.";
        return self::send($toEmail, $subject, $body);
    }

    public static function send(string $to, string $subject, string $body): bool {
        // 1. Fetch settings from DB (dynamic)
        $settingModel = new SiteSettingModel();
        $dbSettings   = $settingModel->getAllAsMap();

        // 2. Resolve final SMTP values (DB takes priority over .env)
        $fromEmail = $dbSettings['smtp_from_email'] ?? Env::get('SMTP_FROM_EMAIL', 'noreply@example.com');
        $fromName  = $dbSettings['smtp_from_name']  ?? Env::get('SMTP_FROM_NAME',  'ClassSystem');
        
        $host     = $dbSettings['smtp_host']       ?? Env::get('SMTP_HOST');
        $port     = (int)($dbSettings['smtp_port'] ?? Env::get('SMTP_PORT', 587));
        $user     = $dbSettings['smtp_username']   ?? Env::get('SMTP_USERNAME');
        $pass     = $dbSettings['smtp_password']   ?? Env::get('SMTP_PASSWORD');
        $encrypt  = $dbSettings['smtp_encryption'] ?? Env::get('SMTP_ENCRYPTION', 'tls');

        // 3. Dev mode check (logs to file instead of sending)
        if (APP_ENV === 'development') {
            error_log("[MAIL_DEBUG] To: {$to} | Subject: {$subject}");
            error_log("[MAIL_BODY]  {$body}");
            return true; 
        }

        // 4. Use custom Socket SMTP if credentials are provided
        if ($host && $user) {
            return self::sendSmtpSocket($to, $subject, $body, $host, $port, $user, $pass, $encrypt, $fromEmail, $fromName);
        }

        // 5. Fallback to native mail()
        $headers  = 'From: ' . $fromName . ' <' . $fromEmail . ">\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        return mail($to, $subject, $body, $headers);
    }

    /**
     * Custom Socket-based SMTP implementation (Ported from Digitak Market App)
     */
    private static function sendSmtpSocket($to, $subject, $message, $host, $port, $user, $pass, $encryption, $fromEmail, $fromName): bool {
        try {
            $siteName = $fromName ?? APP_NAME;
            $ehloHost = $_SERVER['HTTP_HOST'] ?? gethostname() ?: 'localhost';
            $remote   = ($encryption === 'ssl' ? 'ssl://' : '') . $host;

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ]);

            $socket = stream_socket_client($remote . ':' . $port, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
            if (!$socket) {
                error_log("[SMTP_SOCKET_ERROR] Connection Failed: $errstr ($errno)");
                return false;
            }

            $getResponse = function ($socket): string {
                $res = '';
                while ($str = fgets($socket, 515)) {
                    $res .= $str;
                    if (substr($str, 3, 1) === ' ') break;
                }
                return $res;
            };

            $r = $getResponse($socket);
            if (strpos($r, '220') !== 0) { fclose($socket); return false; }

            fwrite($socket, "EHLO $ehloHost\r\n");
            $getResponse($socket);

            if ($encryption === 'tls') {
                fwrite($socket, "STARTTLS\r\n");
                $r = $getResponse($socket);
                if (strpos($r, '220') !== 0) { fclose($socket); return false; }

                $cryptoMethod = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                
                if (!stream_socket_enable_crypto($socket, true, $cryptoMethod)) {
                    fclose($socket);
                    return false;
                }
                fwrite($socket, "EHLO $ehloHost\r\n");
                $getResponse($socket);
            }

            if ($pass !== '') {
                fwrite($socket, "AUTH LOGIN\r\n");
                $getResponse($socket);
                fwrite($socket, base64_encode($user) . "\r\n");
                $getResponse($socket);
                fwrite($socket, base64_encode($pass) . "\r\n");
                $r = $getResponse($socket);
                if (strpos($r, '235') !== 0) { fclose($socket); return false; }
            }

            fwrite($socket, "MAIL FROM: <$user>\r\n");
            $getResponse($socket);
            fwrite($socket, "RCPT TO: <$to>\r\n");
            $getResponse($socket);
            fwrite($socket, "DATA\r\n");
            $r = $getResponse($socket);
            if (strpos($r, '354') !== 0) { fclose($socket); return false; }

            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/plain; charset=utf-8\r\n";
            $headers .= "To: <$to>\r\n";
            $headers .= "From: $siteName <$fromEmail>\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            $headers .= "X-Mailer: PHP/ClassSystemSMTP\r\n";

            fwrite($socket, $headers . "\r\n" . $message . "\r\n.\r\n");
            $r = $getResponse($socket);
            if (strpos($r, '250') !== 0) { fclose($socket); return false; }

            fwrite($socket, "QUIT\r\n");
            fclose($socket);
            return true;

        } catch (Exception $e) {
            error_log("[SMTP_SOCKET_EXCEPTION] " . $e->getMessage());
            return false;
        }
    }
}
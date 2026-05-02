<?php

/**
 * SmsHelper — handles SMS delivery via external gateways.
 * Ported for Sri Lankan standard (94 format).
 */
class SmsHelper {

    /**
     * Send a raw SMS.
     * Integrates with Notify.lk by default.
     */
    public static function send(string $phone, string $message): bool {
        // 1. Normalize phone for Sri Lanka
        $phone = AuthHelper::normalizePhone($phone);

        // 2. Load API credentials (you should add these to your .env)
        $user_id   = Env::get('NOTIFY_USER_ID', '');
        $api_key   = Env::get('NOTIFY_API_KEY', '');
        $sender_id = Env::get('NOTIFY_SENDER_ID', 'NotifyDemo');

        // 3. Log the attempt
        error_log("[SMS_ATTEMPT] To: {$phone} | Msg: {$message}");

        // 4. Skip real sending in development unless keys are set
        if (APP_ENV === 'development' || empty($user_id) || empty($api_key)) {
            // Check your PHP error log to see the code during development!
            return true; 
        }

        // 5. Notify.lk API call
        try {
            $url = "https://app.notify.lk/api/v1/send";
            $params = [
                'user_id'   => $user_id,
                'api_key'   => $api_key,
                'sender_id' => $sender_id,
                'to'        => $phone,
                'message'   => $message
            ];

            $queryString = http_build_query($params);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . "?" . $queryString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            return isset($result['status']) && $result['status'] === 'success';

        } catch (Exception $e) {
            error_log("[SMS_ERROR] " . $e->getMessage());
            return false;
        }
    }

    public static function sendOtp(string $phone, string $code): bool {
        $appName = APP_NAME;
        $message = "Your {$appName} code is: {$code}. Valid for 10 mins.";
        return self::send($phone, $message);
    }
}

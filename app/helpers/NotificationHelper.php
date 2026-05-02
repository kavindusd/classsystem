<?php

class NotificationHelper {

    /**
     * Store an in-app notification for a user.
     *
     * @param int    $recipientUserId  users.id of the recipient
     * @param string $message
     * @param string $senderRole       'admin' | 'teacher'
     * @param int    $senderId         admins.id or teachers.id
     * @param string $recipientRole    'admin' | 'teacher' | 'student'
     */
    public static function send(
        int    $recipientUserId,
        string $message,
        string $senderRole  = 'admin',
        int    $senderId    = 0,
        string $recipientRole = 'student'
    ): void {
        $model = new NotificationModel();
        $model->insert([
            'sender_role'    => $senderRole,
            'sender_id'      => $senderId,
            'recipient_role' => $recipientRole,
            'recipient_id'   => $recipientUserId,
            'message'        => $message,
            'is_read'        => 0,
        ]);

        // Attempt WhatsApp delivery for student recipients
        if ($recipientRole === 'student') {
            self::attemptWhatsAppDelivery($recipientUserId, $message);
        }
    }

    /**
     * Internal helper to delivery messages to WhatsApp if enabled
     */
    private static function attemptWhatsAppDelivery(int $userId, string $message): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT whatsapp_number, whatsapp_enabled FROM students WHERE user_id = ?");
        $stmt->execute([$userId]);
        $student = $stmt->fetch();

        if ($student && $student['whatsapp_enabled'] && $student['whatsapp_number']) {
            $settingModel = new SiteSettingModel();
            $provider = $settingModel->getSetting('whatsapp_provider', 'none');
            $cleanNumber = preg_replace('/[^0-9]/', '', $student['whatsapp_number']);

            if ($provider === 'none') {
                error_log("[WHATSAPP_SIMULATION] To: {$cleanNumber} Message: " . str_replace("\n", " ", $message));
                return;
            }

            // Integration hook for Real WhatsApp API providers
            // Case 'twilio': ...
            // Case 'ultramsg': ...
            error_log("[WHATSAPP_LIVE_PROVIDER:{$provider}] To: {$cleanNumber} Message: " . str_replace("\n", " ", $message));
        }
    }

    /**
     * Deliver a class join link to a student.
     * Returns true if sent, false if skipped due to suspension.
     */
    public static function sendJoinLink(int $studentId, int $courseId, string $link, int $teacherUserId): bool {
        if (!MonthlySlipMiddleware::isActive($studentId, $courseId)) {
            return false;
        }

        $db = Database::getInstance();
        
        // Fetch student & user info
        $stmt = $db->prepare("
            SELECT u.name, u.email, u.phone, s.whatsapp_number, s.whatsapp_enabled 
            FROM students s 
            JOIN users u ON u.id = s.user_id 
            WHERE s.id = ?
        ");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        if (!$student) return true;

        // Fetch course & teacher info
        $stmt = $db->prepare("
            SELECT c.name as course_name, c.subject, c.grade, c.class_start_time, u.name as teacher_name
            FROM courses c
            JOIN teachers t ON t.id = c.teacher_id
            JOIN users u ON u.id = t.user_id
            WHERE c.id = ?
        ");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();
        if (!$course) return true;

        $timeStr = $course['class_start_time'] ? date('h:i A', strtotime($course['class_start_time'])) : 'TBA';
        
        $message = "🚀 *New Class Join Link*\n\n"
                 . "🎓 *Class:* {$course['course_name']}\n"
                 . "📖 *Subject:* {$course['subject']}\n"
                 . "🏷️ *Grade:* {$course['grade']}\n"
                 . "👨‍🏫 *Teacher:* {$course['teacher_name']}\n"
                 . "⏰ *Time:* {$timeStr}\n\n"
                 . "🔗 *Join Link:* {$link}\n\n"
                 . "See you in class!";

        // Send via email
        if ($student['email']) {
            MailHelper::send($student['email'], "Join Link: {$course['course_name']}", str_replace('*', '', $message));
        }

        // Send via WhatsApp (Logic for future integration)
        $whatsappTarget = $student['whatsapp_number'] ?: $student['phone'];
        if ($whatsappTarget && $student['whatsapp_enabled']) {
            // Integration hook for WhatsApp API
            error_log("[WHATSAPP_JOIN_LINK] To: {$whatsappTarget} Message: " . str_replace("\n", " ", $message));
        }

        return true;
    }

    /**
     * Notify all admins about students with suspended access in a course.
     */
    public static function notifyAdminSuspension(int $courseId, int $suspendedCount): void {
        if ($suspendedCount <= 0) return;

        $db = Database::getInstance();
        $course = $db->prepare("SELECT name FROM courses WHERE id = ?");
        $course->execute([$courseId]);
        $courseName = $course->fetchColumn();

        // Get all admin user IDs
        $admins = $db->query("SELECT user_id FROM admins")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($admins as $adminUserId) {
            self::send(
                (int)$adminUserId,
                "ALERT: {$suspendedCount} student(s) have suspended access for '{$courseName}' due to missing payments.",
                'system',
                0,
                'admin'
            );
        }
    }
}
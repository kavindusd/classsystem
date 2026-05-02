<?php

/**
 * Student notifications — view all received notifications and mark as read.
 */
class NotificationController extends Controller {

    public function index(): void {
        (new RoleMiddleware('student'))->handle();

        $user        = $this->currentUser();
        $notifModel  = new NotificationModel();

        // Fetch all notifications
        $notifications = $notifModel->getForUser($user['id']);

        // Mark all as read on viewing
        $notifModel->markAllRead($user['id']);

        $this->view('student/notifications', [
            'notifications' => $notifications,
            'user'          => $user,
        ], 'student_layout');
    }
}

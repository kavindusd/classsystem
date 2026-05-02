<?php

/**
 * Checks whether a student has an approved slip for the current month
 * when teacher sends class join links.
 * This is not a route middleware — it is invoked programmatically
 * inside the NotificationController / join link dispatch logic.
 */
class MonthlySlipMiddleware {

    /**
     * Return true if the student is allowed to receive join links this month.
     *
     * @param int $studentId
     * @param int $courseId
     */
    public static function isActive(int $studentId, int $courseId): bool {
        $slipModel = new SlipModel();
        return $slipModel->hasAccess($studentId, $courseId);
    }
}

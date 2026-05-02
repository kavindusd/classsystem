<?php

/**
 * Monthly payment slips submitted by students per course.
 * Status: pending | approved | rejected
 */
class SlipModel extends Model {
    protected string $table = 'slips';

    /**
     * Check if a student has an approved slip for the current month for a course.
     * Used by MonthlySlipMiddleware to gate join link delivery.
     */
    public function hasApprovedSlipThisMonth(int $studentId, int $courseId): bool {
        $month = date('Y-m');
        $row   = $this->query(
            "SELECT id FROM {$this->table}
             WHERE student_id = ? AND course_id = ? AND slip_month = ? AND status = 'approved'
             LIMIT 1",
            [$studentId, $courseId, $month]
        )->fetch();
        return (bool) $row;
    }

    /**
     * Check if student currently has access (either paid this month OR in grace period with last month paid)
     */
    public function hasAccess(int $studentId, int $courseId): bool {
        // 1. Check current month first (always grants access)
        if ($this->hasApprovedSlipThisMonth($studentId, $courseId)) {
            return true;
        }

        // 2. Check if within grace period and previous month was paid
        $day = (int)date('j');
        if ($day <= (int)SLIP_GRACE_DAYS) {
            $prevMonth = date('Y-m', strtotime('first day of last month'));
            $row = $this->query(
                "SELECT id FROM {$this->table}
                 WHERE student_id = ? AND course_id = ? AND slip_month = ? AND status = 'approved'
                 LIMIT 1",
                [$studentId, $courseId, $prevMonth]
            )->fetch();
            return (bool) $row;
        }

        return false;
    }

    /**
     * Get a student's slip for a specific month and course.
     */
    public function getSlipForMonth(int $studentId, int $courseId, string $month): array|false {
        return $this->query(
            "SELECT * FROM {$this->table}
             WHERE student_id = ? AND course_id = ? AND slip_month = ?
             LIMIT 1",
            [$studentId, $courseId, $month]
        )->fetch();
    }
}

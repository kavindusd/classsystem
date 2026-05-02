<?php

/**
 * Utilities for slip submission and status checks.
 */
class SlipHelper {

    public static function getCurrentMonth(): string {
        return date('Y-m');
    }

    public static function isSubmissionOpen(): bool {
        // Students can submit from SLIP_SUBMISSION_DAY each month
        return (int)date('j') >= SLIP_SUBMISSION_DAY;
    }
}

<?php

/**
 * Class schedule entries — date, time, course.
 */
class ScheduleModel extends Model {
    protected string $table = 'schedules';

    /**
     * Get recurring weekly schedule for a student from enrolled courses' class_days/class_start_time/class_end_time.
     * Returns one entry per course per matching day in the given week.
     */
    public function getWeekForStudent(int $studentId, string $weekStart, string $weekEnd): array {
        // Fetch all enrolled active courses that have a schedule set
        $rows = $this->query(
            "SELECT c.id as course_id, c.name as course_name, c.subject,
                    c.class_days, c.class_start_time, c.class_end_time,
                    u.name as teacher_name
             FROM enrollments e
             JOIN courses c ON c.id = e.course_id
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             WHERE e.student_id = ?
               AND c.status = 'active'
               AND c.class_days IS NOT NULL
               AND c.class_start_time IS NOT NULL",
            [$studentId]
        )->fetchAll();

        return $this->expandToWeek($rows, $weekStart, $weekEnd);
    }

    /**
     * Get recurring weekly schedule for a teacher from their courses.
     */
    public function getWeekForTeacher(int $teacherId, string $weekStart, string $weekEnd): array {
        $rows = $this->query(
            "SELECT c.id as course_id, c.name as course_name, c.subject,
                    c.class_days, c.class_start_time, c.class_end_time
             FROM courses c
             WHERE c.teacher_id = ?
               AND c.status = 'active'
               AND c.class_days IS NOT NULL
               AND c.class_start_time IS NOT NULL",
            [$teacherId]
        )->fetchAll();

        return $this->expandToWeek($rows, $weekStart, $weekEnd);
    }

    /**
     * Expand recurring course records into concrete class_date entries for the given week.
     */
    private function expandToWeek(array $rows, string $weekStart, string $weekEnd): array {
        // Day-of-week short code => PHP date 'D' format output
        $dayMap = [
            'Mon' => 'Mon', 'Tue' => 'Tue', 'Wed' => 'Wed',
            'Thu' => 'Thu', 'Fri' => 'Fri', 'Sat' => 'Sat', 'Sun' => 'Sun',
        ];

        $results = [];
        $current = strtotime($weekStart);
        $end     = strtotime($weekEnd);

        // Build a list of each day in the week keyed by short day name
        $weekDays = [];
        while ($current <= $end) {
            $shortName           = date('D', $current); // Mon, Tue, ...
            $weekDays[$shortName] = date('Y-m-d', $current);
            $current             = strtotime('+1 day', $current);
        }

        foreach ($rows as $c) {
            $days = array_filter(array_map('trim', explode(',', $c['class_days'])));
            foreach ($days as $d) {
                if (isset($weekDays[$d])) {
                    $results[] = [
                        'course_id'        => $c['course_id'],
                        'course_name'      => $c['course_name'],
                        'subject'          => $c['subject'],
                        'teacher_name'     => $c['teacher_name'] ?? null,
                        'class_date'       => $weekDays[$d],
                        'class_start_time' => $c['class_start_time'],
                        'class_end_time'   => $c['class_end_time'],
                        'class_days'       => $c['class_days'],
                    ];
                }
            }
        }

        // Sort by date then start time
        usort($results, fn($a, $b) =>
            ($a['class_date'] . $a['class_start_time']) <=> ($b['class_date'] . $b['class_start_time'])
        );

        return $results;
    }
}

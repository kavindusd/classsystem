<?php

class ScheduleController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $courseId = (int) Request::get('course_id', 0);
        $db       = Database::getInstance();

        // All courses for filter dropdown
        $courses = $db->query(
            "SELECT c.id, c.name, c.subject, c.grade, u.name as teacher_name
             FROM courses c
             JOIN teachers t ON t.id = c.teacher_id
             JOIN users u ON u.id = t.user_id
             ORDER BY c.name"
        )->fetchAll();

        // Schedules filtered by course (from schedules table)
        $sql    = "SELECT s.*, c.name as course_name, c.subject, u.name as teacher_name
                   FROM schedules s
                   JOIN courses c ON c.id = s.course_id
                   JOIN teachers t ON t.id = c.teacher_id
                   JOIN users u ON u.id = t.user_id";
        $params = [];

        if ($courseId) {
            $sql    .= " WHERE s.course_id = ?";
            $params[] = $courseId;
        }

        $sql .= " ORDER BY s.class_date DESC, s.start_time ASC";

        $stmt      = $db->prepare($sql);
        $stmt->execute($params);
        $schedules = $stmt->fetchAll();

        // Fallback: teachers configure weekly timings via courses.class_days/class_start_time/class_end_time.
        // If the schedules table is empty, expand course settings for the current week so admin can still see them.
        if (empty($schedules)) {
            $weekStart = date('Y-m-d', strtotime('monday this week'));
            $weekEnd   = date('Y-m-d', strtotime('sunday this week'));

            $dayMap = [
                'Mon' => 'Mon', 'Tue' => 'Tue', 'Wed' => 'Wed',
                'Thu' => 'Thu', 'Fri' => 'Fri', 'Sat' => 'Sat', 'Sun' => 'Sun',
            ];

            // Fetch active courses with schedule settings
            $courseSql = "SELECT c.id as course_id, c.name as course_name, c.subject,
                                 c.class_days, c.class_start_time, c.class_end_time,
                                 u.name as teacher_name
                          FROM courses c
                          JOIN teachers t ON t.id = c.teacher_id
                          JOIN users u ON u.id = t.user_id
                          WHERE c.status = 'active'
                            AND c.class_days IS NOT NULL
                            AND c.class_start_time IS NOT NULL";
            $courseParams = [];

            if ($courseId) {
                $courseSql .= " AND c.id = ?";
                $courseParams[] = $courseId;
            }

            $courseSql .= " ORDER BY c.name";

            $courseRows = $db->prepare($courseSql);
            $courseRows->execute($courseParams);
            $rows = $courseRows->fetchAll();

            // Build concrete week dates keyed by short day
            $weekDays = [];
            $current  = strtotime($weekStart);
            $end      = strtotime($weekEnd);
            while ($current <= $end) {
                $shortName = date('D', $current); // Mon..Sun
                $weekDays[$shortName] = date('Y-m-d', $current);
                $current = strtotime('+1 day', $current);
            }

            $expanded = [];
            foreach ($rows as $c) {
                $days = array_filter(array_map('trim', explode(',', (string)$c['class_days'])));
                foreach ($days as $d) {
                    if (isset($weekDays[$d])) {
                        $expanded[] = [
                            'id' => null,
                            'course_id' => (int)$c['course_id'],
                            'course_name' => $c['course_name'],
                            'subject' => $c['subject'],
                            'teacher_name' => $c['teacher_name'] ?? null,
                            'class_date' => $weekDays[$d],
                            'start_time' => $c['class_start_time'],
                            'end_time' => $c['class_end_time'],
                            'notes' => '—',
                        ];
                    }
                }
            }

            usort($expanded, fn($a, $b) =>
                ($a['class_date'] . $a['start_time']) <=> ($b['class_date'] . $b['start_time'])
            );

            $schedules = $expanded;
        }

        $this->view('admin/schedules', [
            'schedules'         => $schedules,
            'courses'           => $courses,
            'filter_course_id'  => $courseId,
        ], 'admin_layout');
    }

    public function create(): void {
        (new RoleMiddleware('admin'))->handle();

        $courseId  = (int) Request::post('course_id');
        $classDate = Request::post('class_date');
        $startTime = Request::post('start_time');
        $endTime   = Request::post('end_time');
        $notes     = Request::sanitize(Request::post('notes', ''));

        if (!$courseId || !$classDate || !$startTime || !$endTime) {
            Session::flash('error', 'Course, date, start time, and end time are required.');
            $this->redirect('admin/schedules');
        }

        if ($startTime >= $endTime) {
            Session::flash('error', 'End time must be after start time.');
            $this->redirect('admin/schedules');
        }

        $db = Database::getInstance();
        $db->prepare(
            "INSERT INTO schedules (course_id, class_date, start_time, end_time, notes)
             VALUES (?, ?, ?, ?, ?)"
        )->execute([$courseId, $classDate, $startTime, $endTime, $notes ?: null]);

        Session::flash('success', 'Schedule entry added.');
        $this->redirect('admin/schedules');
    }

    public function delete(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $db   = Database::getInstance();
        $row  = $db->prepare("SELECT id FROM schedules WHERE id = ? LIMIT 1");
        $row->execute([(int)$id]);

        if (!$row->fetch()) {
            Session::flash('error', 'Schedule entry not found.');
            $this->redirect('admin/schedules');
        }

        $db->prepare("DELETE FROM schedules WHERE id = ?")->execute([(int)$id]);

        Session::flash('success', 'Schedule entry deleted.');
        $this->redirect('admin/schedules');
    }
}

<?php

/**
 * Student weekly class schedule — shows only enrolled courses with an approved slip this month.
 */
class ScheduleController extends Controller {

    public function index(): void {
        (new RoleMiddleware('student'))->handle();

        $user         = $this->currentUser();
        $studentModel = new StudentModel();
        $student      = $studentModel->findByUserId($user['id']);
        if (!$student) $this->abort(403);

        // Default: current week
        $weekOffset = (int) Request::get('week', 0);
        $weekStart  = date('Y-m-d', strtotime("monday this week +{$weekOffset} weeks"));
        $weekEnd    = date('Y-m-d', strtotime("sunday this week +{$weekOffset} weeks"));

        $scheduleModel = new ScheduleModel();
        $schedules     = $scheduleModel->getWeekForStudent($student['id'], $weekStart, $weekEnd);

        // Group by date
        $byDate = [];
        foreach ($schedules as $s) {
            $byDate[$s['class_date']][] = $s;
        }
        ksort($byDate);

        $this->view('student/schedule', [
            'byDate'      => $byDate,
            'weekStart'   => $weekStart,
            'weekEnd'     => $weekEnd,
            'weekOffset'  => $weekOffset,
            'student'     => $student,
            'user'        => $user,
        ], 'student_layout');
    }
}

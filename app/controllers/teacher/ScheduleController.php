<?php

/**
 * Teacher weekly class schedule
 */
class ScheduleController extends Controller {

    public function index(): void {
        (new RoleMiddleware('teacher'))->handle();

        $user         = $this->currentUser();
        $teacherModel = new TeacherModel();
        $teacher      = $teacherModel->findByUserId($user['id']);
        if (!$teacher) $this->abort(403);

        // Default: current week
        $weekOffset = (int) Request::get('week', 0);
        $weekStart  = date('Y-m-d', strtotime("monday this week +{$weekOffset} weeks"));
        $weekEnd    = date('Y-m-d', strtotime("sunday this week +{$weekOffset} weeks"));

        $scheduleModel = new ScheduleModel();
        $schedules     = $scheduleModel->getWeekForTeacher($teacher['id'], $weekStart, $weekEnd);

        // Group by date
        $byDate = [];
        foreach ($schedules as $s) {
            $byDate[$s['class_date']][] = $s;
        }
        ksort($byDate);

        $this->view('teacher/schedule', [
            'byDate'      => $byDate,
            'weekStart'   => $weekStart,
            'weekEnd'     => $weekEnd,
            'weekOffset'  => $weekOffset,
        ], 'teacher_layout');
    }
}

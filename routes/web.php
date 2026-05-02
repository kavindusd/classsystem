<?php
/**
 * All application routes.
 * Format: $router->get('path', 'ControllerName@method')
 *         $router->post('path', 'ControllerName@method')
 * 
 * Route Groups:
 *   /              — Public (auth)
 *   /admin/*       — Admin panel
 *   /teacher/*     — Teacher panel
 *   /student/*     — Student panel
 */

// ─── Public / Auth ───────────────────────────────────────────────
$router->get('',                    'AuthController@showLogin');
$router->get('login',               'AuthController@showLogin');
$router->post('login',              'AuthController@login');
$router->get('register',            'AuthController@showRegister');
$router->post('register',           'AuthController@register');
$router->get('verify-otp',          'AuthController@showVerifyOtp');
$router->post('verify-otp',         'AuthController@verifyOtp');
$router->get('forgot-password',     'AuthController@showForgotPassword');
$router->post('forgot-password',    'AuthController@forgotPassword');
$router->get('reset-password',      'AuthController@showResetPassword');
$router->post('reset-password',     'AuthController@resetPassword');
$router->get('logout',              'AuthController@logout');

// ─── Admin ───────────────────────────────────────────────────────
$router->get('admin',                            'admin\DashboardController@index');
$router->get('admin/teachers',                   'admin\TeacherController@index');
$router->post('admin/teachers/create',           'admin\TeacherController@create');
$router->post('admin/teachers/update/:id',       'admin\TeacherController@update');
$router->post('admin/teachers/delete/:id',       'admin\TeacherController@delete');

$router->get('admin/students',                   'admin\StudentController@index');
$router->post('admin/students/create',           'admin\StudentController@create');
$router->post('admin/students/update/:id',       'admin\StudentController@update');
$router->post('admin/students/delete/:id',       'admin\StudentController@delete');

$router->get('admin/courses',                    'admin\CourseController@index');
$router->post('admin/courses/create',            'admin\CourseController@create');
$router->post('admin/courses/update/:id',        'admin\CourseController@update');
$router->post('admin/courses/delete/:id',        'admin\CourseController@delete');

$router->get('admin/slips',                      'admin\SlipController@index');
$router->post('admin/slips/approve/:id',         'admin\SlipController@approve');
$router->post('admin/slips/reject/:id',          'admin\SlipController@reject');

$router->get('admin/earnings',                   'admin\AdminEarningsController@index');

$router->get('admin/notifications',              'admin\NotificationController@index');
$router->post('admin/notifications/send',        'admin\NotificationController@send');

$router->get('admin/settings',                   'admin\SettingsController@index');
$router->post('admin/settings/site',             'admin\SettingsController@updateSite');
$router->post('admin/settings/smtp',             'admin\SettingsController@updateSmtp');

$router->get('admin/admins',                     'admin\AdminController@index');
$router->post('admin/admins/create',             'admin\AdminController@create');
$router->post('admin/admins/delete/:id',         'admin\AdminController@delete');
$router->post('admin/admins/change-password',    'admin\AdminController@changePassword');

// ─── Admin Schedules ──────────────────────────────────────────────
$router->get('admin/schedules',                  'admin\ScheduleController@index');
$router->post('admin/schedules/create',          'admin\ScheduleController@create');
$router->post('admin/schedules/delete/:id',      'admin\ScheduleController@delete');

// ─── Admin Enrollments ───────────────────────────────────────────
$router->get('admin/enrollments',                'admin\EnrollmentController@index');
$router->post('admin/enrollments/remove/:id',    'admin\EnrollmentController@remove');

// ─── Admin Grades Overview ────────────────────────────────────────
$router->get('admin/grades',                     'admin\GradeController@index');

// ─── Teacher ─────────────────────────────────────────────────────
$router->get('teacher',                          'teacher\DashboardController@index');
$router->get('teacher/schedule',                 'teacher\ScheduleController@index');
$router->get('teacher/courses',                  'teacher\CourseController@index');
$router->get('teacher/courses/:id',              'teacher\CourseController@show');
$router->post('teacher/courses/:id/update',      'teacher\CourseController@update');
$router->post('teacher/courses/:id/join-link',   'teacher\CourseController@sendJoinLink');
$router->get('teacher/students',                 'teacher\StudentController@index');

$router->get('teacher/earnings',                 'teacher\TeacherEarningsController@index');

$router->get('teacher/grading',                  'teacher\GradingController@index');
$router->get('teacher/grading/:courseId',        'teacher\GradingController@course');
$router->post('teacher/grading/:courseId/exam/create',           'teacher\GradingController@createExam');
$router->post('teacher/grading/:courseId/exam/:examId/grade',    'teacher\GradingController@saveGrades');

// Alias: /teacher/exams maps to the same grading controller
$router->get('teacher/exams',                    'teacher\GradingController@index');
$router->get('teacher/exams/:courseId',          'teacher\GradingController@course');
$router->post('teacher/exams/:courseId/exam/create',          'teacher\GradingController@createExam');
$router->post('teacher/exams/:courseId/exam/:examId/grade',   'teacher\GradingController@saveGrades');


$router->get('teacher/notifications',            'teacher\NotificationController@index');
$router->post('teacher/notifications/send',      'teacher\NotificationController@send');

$router->get('teacher/settings',                 'teacher\SettingsController@index');
$router->post('teacher/settings/update',         'teacher\SettingsController@update');
$router->post('teacher/settings/change-password','teacher\SettingsController@changePassword');

// ─── Student ─────────────────────────────────────────────────────
$router->get('student',                          'student\DashboardController@index');
$router->get('student/schedule',                 'student\ScheduleController@index');

$router->get('student/courses',                  'student\CourseController@index');
$router->get('student/courses/search',           'student\CourseController@search');
$router->get('student/courses/:id',              'student\CourseController@show');

$router->get('student/slips',                    'student\SlipController@index');
$router->post('student/slips/:courseId/submit',  'student\SlipController@submit');

$router->get('student/grading',                  'student\GradingController@index');
$router->get('student/grading/:courseId',        'student\GradingController@course');
$router->get('student/grading/:courseId/exam/:examId', 'student\GradingController@exam');

$router->get('student/notifications',            'student\NotificationController@index');

$router->get('student/settings',                 'student\SettingsController@index');
$router->post('student/settings/update',         'student\SettingsController@update');
$router->post('student/settings/change-password','student\SettingsController@changePassword');

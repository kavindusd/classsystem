<?php

class SlipController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $status  = Request::get('status', 'pending');
        $month   = Request::get('month', date('Y-m'));
        $db      = Database::getInstance();

        $sql    = "SELECT s.*, u.name as student_name, st.student_id,
                          c.name as course_name, c.subject
                   FROM slips s
                   JOIN students st ON st.id = s.student_id
                   JOIN users u ON u.id = st.user_id
                   JOIN courses c ON c.id = s.course_id
                   WHERE s.slip_month = ?";
        $params = [$month];

        if ($status !== 'all') {
            $sql    .= " AND s.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY s.submitted_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $slips = $stmt->fetchAll();

        $this->view('admin/slips', [
            'slips'  => $slips,
            'status' => $status,
            'month'  => $month,
        ], 'admin_layout');
    }

    public function approve(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $slipModel = new SlipModel();
        $slip      = $slipModel->findById((int)$id);

        if (!$slip || $slip['status'] !== 'pending') {
            Session::flash('error', 'Slip not found or already processed.');
            $this->redirect('admin/slips');
        }

        // Update slip status
        $slipModel->update((int)$id, [
            'status'      => 'approved',
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        // Create enrollment if first approved slip for this student+course
        $enrollModel = new EnrollmentModel();
        $existing    = $enrollModel->findEnrollment($slip['student_id'], $slip['course_id']);
        if (!$existing) {
            $enrollModel->insert([
                'student_id' => $slip['student_id'],
                'course_id'  => $slip['course_id'],
            ]);
        }

        // Notify student
        $studentModel = new StudentModel();
        $student      = $studentModel->findById($slip['student_id']);
        NotificationHelper::send(
            $student['user_id'],
            "Your payment slip for " . date('F Y', strtotime($slip['slip_month'] . '-01')) . " has been approved.",
            'admin',
            0,
            'student'
        );

        Session::flash('success', 'Slip approved and student enrolled.');
        $this->redirect('admin/slips');
    }

    public function reject(string $id): void {
        (new RoleMiddleware('admin'))->handle();

        $slipModel = new SlipModel();
        $slip      = $slipModel->findById((int)$id);

        if (!$slip || $slip['status'] !== 'pending') {
            Session::flash('error', 'Slip not found or already processed.');
            $this->redirect('admin/slips');
        }

        $reason = Request::sanitize(Request::post('reason', 'Slip rejected by admin.'));

        $slipModel->update((int)$id, [
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_at'      => date('Y-m-d H:i:s'),
        ]);

        // Notify student
        $studentModel = new StudentModel();
        $student      = $studentModel->findById($slip['student_id']);
        NotificationHelper::send(
            $student['user_id'],
            "Your payment slip for " . date('F Y', strtotime($slip['slip_month'] . '-01')) . " was rejected. Reason: {$reason}",
            'admin',
            0,
            'student'
        );

        Session::flash('success', 'Slip rejected. Student has been notified.');
        $this->redirect('admin/slips');
    }
}
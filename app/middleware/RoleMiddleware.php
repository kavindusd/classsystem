<?php

class RoleMiddleware extends Middleware {
    private string $requiredRole;

    public function __construct(string $role) {
        $this->requiredRole = $role;
    }

    /**
     * Ensure the logged-in user has the required role.
     * Roles: 'admin', 'teacher', 'student'
     */
    public function handle(): void {
        (new AuthMiddleware())->handle();

        if (Session::get('role') !== $this->requiredRole) {
            http_response_code(403);
            $view = APP . '/views/shared/403.php';
            if (file_exists($view)) require_once $view;
            else echo '<h1>403 — Forbidden</h1>';
            exit;
        }
    }
}

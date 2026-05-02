<?php

abstract class Controller {

    /**
     * Render a view with optional layout.
     * 
     * @param string $view    Path relative to app/views/ e.g. 'student/dashboard'
     * @param array  $data    Variables to extract into view scope
     * @param string $layout  Layout name inside app/views/layouts/ (without .php)
     */
    protected function view(string $view, array $data = [], string $layout = ''): void {
        // Automatically inject common data if not provided
        if (!isset($data['user'])) {
            $data['user'] = $this->currentUser();
        }
        if (!isset($data['role'])) {
            $data['role'] = $this->currentRole();
        }
        
        // Inject site settings
        $settingsModel = new SiteSettingModel();
        $data['site_settings'] = $settingsModel->getAllAsMap();

        extract($data);

        $viewFile = APP . '/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            $this->abort(404);
        }

        if ($layout) {
            $layoutFile = APP . '/views/layouts/' . $layout . '.php';
            $content    = $viewFile; // layout includes this via $content variable
            require_once $layoutFile;
        } else {
            require_once $viewFile;
        }
    }

    /**
     * Return JSON response.
     */
    protected function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url): void {
        header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
        exit;
    }

    /**
     * Abort with HTTP error page.
     */
    protected function abort(int $code = 404): void {
        http_response_code($code);
        $errorView = APP . '/views/shared/' . $code . '.php';
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            echo "<h1>Error {$code}</h1>";
        }
        exit;
    }

    /**
     * Get current logged-in user from session.
     */
    protected function currentUser(): array|null {
        return Session::get('user') ?? null;
    }

    /**
     * Get current user role.
     */
    protected function currentRole(): string|null {
        return Session::get('role') ?? null;
    }
}

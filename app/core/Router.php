<?php

class Router {
    private array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, string $controllerAction): void {
        $this->addRoute('GET', $path, $controllerAction);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, string $controllerAction): void {
        $this->addRoute('POST', $path, $controllerAction);
    }

    private function addRoute(string $method, string $path, string $controllerAction): void {
        // Convert :param placeholders to named regex groups
        $pattern = preg_replace('/:([a-zA-Z_]+)/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . trim($pattern, '/') . '$#';
        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'action'  => $controllerAction,
        ];
    }

    /**
     * Dispatch the current request to the matching route.
     */
    public function dispatch(): void {
        $url    = trim(Request::url(), '/');
        $method = Request::method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $url, $matches)) {
                // Extract named params only
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controllerName, $action] = explode('@', $route['action']);

                // Extract path and actual class name (e.g. admin\DashboardController -> admin/DashboardController.php and DashboardController)
                $controllerPath = str_replace('\\', '/', $controllerName);
                $className      = basename($controllerPath);

                // Explicitly require the correct controller file to avoid autoloader conflicts
                // since multiple directories have identically named classes without namespaces.
                $file = APP . '/controllers/' . $controllerPath . '.php';
                if (file_exists($file)) {
                    require_once $file;
                } else {
                    // Fallback to core controllers if not found in subdirectories
                    $file = APP . '/controllers/' . $className . '.php';
                    if (file_exists($file)) {
                        require_once $file;
                    }
                }

                if (!class_exists($className, false)) {
                    $this->notFound();
                    return;
                }

                $controller = new $className();

                if (!method_exists($controller, $action)) {
                    $this->notFound();
                    return;
                }

                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        $this->notFound();
    }

    private function notFound(): void {
        http_response_code(404);
        $view = APP . '/views/shared/404.php';
        if (file_exists($view)) require_once $view;
        else echo '<h1>404 — Page Not Found</h1>';
    }
}

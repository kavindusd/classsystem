<?php

abstract class Middleware {
    abstract public function handle(): void;

    /**
     * Redirect helper available to all middleware.
     */
    protected function redirect(string $url): void {
        header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
        exit;
    }
}

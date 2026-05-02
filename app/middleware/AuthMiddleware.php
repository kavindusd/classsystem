<?php

class AuthMiddleware extends Middleware {
    /**
     * Ensure the user is logged in.
     * Redirects to login page if not authenticated.
     */
    public function handle(): void {
        if (!Session::has('user') || !Session::has('role')) {
            Session::flash('error', 'Please log in to continue.');
            $this->redirect('login');
        }
    }
}

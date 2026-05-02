<?php

class SettingsController extends Controller {

    public function index(): void {
        (new RoleMiddleware('admin'))->handle();

        $settingModel = new SiteSettingModel();
        $rawSettings  = $settingModel->findAll();

        // Convert to key => value map
        $settings = [];
        foreach ($rawSettings as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $this->view('admin/settings', ['settings' => $settings], 'admin_layout');
    }

    public function updateSite(): void {
        (new RoleMiddleware('admin'))->handle();

        $siteName = Request::sanitize(Request::post('site_name'));
        $siteLogo = $_FILES['site_logo'] ?? null;
        $favicon  = $_FILES['favicon'] ?? null;

        $settingModel = new SiteSettingModel();
        $settingModel->setSetting('site_name', $siteName);

        // Handle Site Logo
        if ($siteLogo && $siteLogo['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/png', 'image/jpeg', 'image/svg+xml'];
            if (in_array($siteLogo['type'], $allowedTypes)) {
                $ext      = pathinfo($siteLogo['name'], PATHINFO_EXTENSION);
                $filename = 'logo.' . $ext;
                $dest     = ROOT . '/public/assets/images/' . $filename;
                if (move_uploaded_file($siteLogo['tmp_name'], $dest)) {
                    $settingModel->setSetting('site_logo', $filename);
                }
            }
        }

        // Handle Favicon
        if ($favicon && $favicon['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/x-icon', 'image/png', 'image/jpeg'];
            if (in_array($favicon['type'], $allowedTypes)) {
                $ext      = pathinfo($favicon['name'], PATHINFO_EXTENSION);
                $filename = 'favicon.' . $ext;
                $dest     = ROOT . '/public/assets/images/' . $filename;
                if (move_uploaded_file($favicon['tmp_name'], $dest)) {
                    $settingModel->setSetting('site_favicon', $filename);
                }
            }
        }

        Session::flash('success', 'Site settings updated.');
        $this->redirect('admin/settings');
    }

    public function updateSmtp(): void {
        (new RoleMiddleware('admin'))->handle();

        $settingModel = new SiteSettingModel();

        $fields = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'smtp_from_email', 'smtp_from_name'];
        foreach ($fields as $field) {
            $value = Request::post($field, '');
            // Don't overwrite SMTP password if field is left blank
            if ($field === 'smtp_password' && $value === '') continue;
            $settingModel->setSetting($field, $value);
        }

        Session::flash('success', 'SMTP settings updated.');
        $this->redirect('admin/settings');
    }

    public function updateWhatsApp(): void {
        (new RoleMiddleware('admin'))->handle();

        $settingModel = new SiteSettingModel();

        $fields = ['whatsapp_provider', 'whatsapp_api_url', 'whatsapp_api_key', 'whatsapp_instance_id'];
        foreach ($fields as $field) {
            $value = Request::post($field, '');
            if ($field === 'whatsapp_api_key' && $value === '') continue;
            $settingModel->setSetting($field, $value);
        }

        // Handle Phone Login Toggle
        $phoneLogin = Request::post('phone_login_enabled', '0');
        $settingModel->setSetting('phone_login_enabled', $phoneLogin);

        Session::flash('success', 'WhatsApp and Authentication settings deployed.');
        $this->redirect('admin/settings');
    }
}
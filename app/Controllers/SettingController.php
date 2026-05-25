<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    private Setting $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->settingModel = new Setting();
    }

    public function index(): void
    {
        $settings = $this->settingModel->getAllSettings();

        $this->view('settings/index', [
            'title' => 'Settings',
            'settings' => $settings,
        ]);
    }

    public function update(): void
    {
        $this->requireCsrf();

        $input = $this->allInput();

        $settings = [
            'store_name' => trim($input['store_name'] ?? ''),
            'store_address' => trim($input['store_address'] ?? ''),
            'store_phone' => trim($input['store_phone'] ?? ''),
            'tax_rate' => trim($input['tax_rate'] ?? '0'),
            'currency' => trim($input['currency'] ?? 'Rp'),
            'receipt_footer' => trim($input['receipt_footer'] ?? ''),
            'session_timeout' => trim($input['session_timeout'] ?? '30'),
        ];

        $this->settingModel->updateMultiple($settings);

        $this->setFlash('success', 'Settings updated successfully');
        $this->redirect('settings');
    }
}

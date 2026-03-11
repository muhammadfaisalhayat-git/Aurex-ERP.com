<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    public function index()
    {
        return view('admin.activation.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_server_address' => 'required|url',
            'deployment_url' => 'required|url',
        ]);

        \App\Models\SystemSetting::updateOrInsert(
            ['key' => 'master_server_address'],
            [
                'value' => $request->master_server_address,
                'type' => 'string',
                'group' => 'system',
                'display_name_en' => 'Master Server Address',
                'display_name_ar' => 'عنوان الخادم الرئيسي',
                'is_editable' => 1
            ]
        );

        \App\Models\SystemSetting::updateOrInsert(
            ['key' => 'deployment_url'],
            [
                'value' => $request->deployment_url,
                'type' => 'string',
                'group' => 'system',
                'display_name_en' => 'Deployment URL',
                'display_name_ar' => 'رابط النشر',
                'is_editable' => 1
            ]
        );

        return redirect()->route('acp.dashboard')->with('success', 'Application activated successfully!');
    }
}

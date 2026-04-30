<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Require setting.manage permission if exists, otherwise open to all admins
        if (\Illuminate\Support\Facades\Gate::has('setting.manage')) {
            \Illuminate\Support\Facades\Gate::authorize('setting.manage');
        }

        $settings = \App\Models\Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        if (\Illuminate\Support\Facades\Gate::has('setting.manage')) {
            \Illuminate\Support\Facades\Gate::authorize('setting.manage');
        }

        $data = $request->except(['_token', '_method']);
        
        foreach ($data as $key => $value) {
            \App\Models\Setting::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'Cập nhật cấu hình thành công!');
    }
}

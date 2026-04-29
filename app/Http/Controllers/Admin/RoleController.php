<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Section;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function index()
    {
        Gate::authorize('roles.view');
        $roles = Role::withCount('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        Gate::authorize('roles.create');
        $sections = Section::all()->groupBy('group');
        return view('admin.roles.create', compact('sections'));
    }

    public function store(Request $request)
    {
        Gate::authorize('roles.create');
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'caption' => 'required|string',
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'caption' => $request->caption,
                'is_admin' => $request->has('is_admin')
            ]);

            if ($request->has('permissions') && !$role->is_admin) {
                $permissions = [];
                $now = now();
                foreach ($request->permissions as $section_id) {
                    $permissions[] = [
                        'role_id' => $role->id,
                        'section_id' => $section_id,
                        'allow' => true,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
                Permission::insert($permissions);
            }

            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Thêm vai trò thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function edit(Role $role)
    {
        Gate::authorize('roles.update');
        $sections = Section::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('section_id')->toArray();

        return view('admin.roles.edit', compact('role', 'sections', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        Gate::authorize('roles.update');
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'caption' => 'required|string',
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name,
                'caption' => $request->caption,
                'is_admin' => $request->has('is_admin')
            ]);

            // Xóa quyền cũ
            Permission::where('role_id', $role->id)->delete();

            // Thêm quyền mới
            if ($request->has('permissions') && !$role->is_admin) {
                $permissions = [];
                $now = now();
                foreach ($request->permissions as $section_id) {
                    $permissions[] = [
                        'role_id' => $role->id,
                        'section_id' => $section_id,
                        'allow' => true,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
                Permission::insert($permissions);
            }

            DB::commit();
            \Illuminate\Support\Facades\Cache::forget("user_permissions_{$role->id}");
            return redirect()->route('admin.roles.index')->with('success', 'Cập nhật vai trò thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        Gate::authorize('roles.delete');
        if ($role->is_admin && Role::where('is_admin', true)->count() <= 1) {
             return back()->with('error', 'Không thể xóa quyền Admin duy nhất');
        }
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Xóa vai trò thành công');
    }
}

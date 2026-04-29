<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        // Kiểm tra tính năng render_diffs trong chat
        Gate::authorize('users.view');
        $users = User::with('role')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        Gate::authorize('users.create');
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        Gate::authorize('users.create');
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'required|string|in:active,blocked'
        ]);

        $role_name = null;
        if ($request->role_id) {
            $role_name = Role::find($request->role_id)->caption;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'role_name' => $role_name,
            'status' => $request->status
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công');
    }

    public function edit(User $user)
    {
        Gate::authorize('users.update');
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('users.update');
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'required|string|in:active,blocked'
        ]);

        $role_name = null;
        if ($request->role_id) {
            $role_name = Role::find($request->role_id)->caption;
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'role_name' => $role_name,
            'status' => $request->status
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công');
    }

    public function destroy(User $user)
    {
        Gate::authorize('users.delete');
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể tự xóa tài khoản của chính mình.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công');
    }
}

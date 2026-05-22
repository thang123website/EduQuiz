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
    public function index(Request $request)
    {
        Gate::authorize('users.view');
        
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
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
            'status' => 'required|string|in:active,blocked',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
            'avatar' => 'nullable|string',
            'cover_photo' => 'nullable|string',
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
            'status' => $request->status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'avatar' => $request->avatar,
            'cover_photo' => $request->cover_photo,
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
            'status' => 'required|string|in:active,blocked',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
            'avatar' => 'nullable|string',
            'cover_photo' => 'nullable|string',
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
            'status' => $request->status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'avatar' => $request->avatar,
            'cover_photo' => $request->cover_photo,
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

    public function bulkDestroy(Request $request)
    {
        Gate::authorize('users.delete');
        $ids = $request->ids;
        
        if (!$ids || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.'
            ], 400);
        }

        if (in_array(auth()->id(), $ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tự xóa tài khoản của chính mình trong danh sách chọn.'
            ], 403);
        }

        try {
            User::whereIn('id', $ids)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thành công ' . count($ids) . ' người dùng.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}

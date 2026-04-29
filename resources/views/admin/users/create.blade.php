@extends('admin.layouts.master')

@section('title', 'Thêm mới User')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Thêm mới Người dùng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                        <li class="breadcrumb-item active">Thêm mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin người dùng</h5>
                    </div>
                    <div class="card-body">
                        <!-- Hàng 1: Tên và Email -->
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Nhập họ và tên" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="example@gmail.com" required>
                                </div>
                            </div>
                        </div>

                        <!-- Hàng 2: Mật khẩu -->
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Xác nhận lại mật khẩu" required>
                                </div>
                            </div>
                        </div>

                        <!-- Hàng 3: Vai trò và Trạng thái -->
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="role_id">Vai trò (Phân quyền)</label>
                                    <select class="form-select" id="role_id" name="role_id">
                                        <option value="">Chọn vai trò...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->caption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="account_status">Trạng thái tài khoản</label>
                                    <select class="form-select" id="account_status" name="status">
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Bị chặn</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-success">Thêm mới</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

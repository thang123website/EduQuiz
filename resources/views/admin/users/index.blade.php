@extends('admin.layouts.master')

@section('title', 'Quản lý Người dùng')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý Người dùng</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Quản lý người dùng</a></li>
                        <li class="breadcrumb-item active">Người dùng</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách người dùng</h4>
                    @can('users.create')
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary waves-effect waves-light"><i class="ri-add-line align-bottom me-1"></i> Thêm mới</a>
                    </div>
                    @endcan
                </div><!-- end card header -->

                <div class="card-body border-bottom-dashed border-bottom">
                    <form action="{{ route('admin.users.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xl-6">
                                <div class="search-box">
                                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, email..." value="{{ request('search') }}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xl-3">
                                <div>
                                    <select class="form-control" name="role_id">
                                        <option value="">Tất cả vai trò</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->caption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xl-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Hiển thị kết quả</button>
                                    @if(request()->has('search') || request()->has('role_id'))
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-light w-100">Xoá lọc</a>
                                    @endif
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Tên hiển thị</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phân quyền</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Ngày tạo</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="chk_child" value="{{ $user->id }}">
                                        </div>
                                    </th>
                                    <td>{{ Str::limit($user->id, 8) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $user->avatar_url }}" alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">
                                            </div>
                                            <div class="flex-grow-1 ms-2 name">{{ $user->name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role)
                                            <span class="badge {{ $user->role->is_admin ? 'bg-danger' : 'bg-success' }}">{{ $user->role->caption }}</span>
                                        @else
                                            <span class="badge bg-secondary">Chưa cấp quyền</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->status == 'active')
                                            <span class="badge bg-success-subtle text-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Bị chặn</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('users.update')
                                            <div class="edit">
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary edit-item-btn"><i class="ri-edit-2-line"></i> Chỉnh sửa</a>
                                            </div>
                                            @endcan
                                            @can('users.delete')
                                            <div class="remove">
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger confirm-delete"><i class="ri-delete-bin-line"></i> Xoá</button>
                                                </form>
                                            </div>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->
@endsection

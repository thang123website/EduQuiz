@extends('admin.layouts.master')

@section('title', 'Phân quyền')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Phân quyền</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Quản lý người dùng</a></li>
                        <li class="breadcrumb-item active">Phân quyền</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách Role</h4>
                    @can('roles.create')
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary waves-effect waves-light"><i class="ri-add-line align-bottom me-1"></i> Thêm mới</a>
                    </div>
                    @endcan
                </div><!-- end card header -->

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
                                    <th scope="col">Tên vai trò</th>
                                    <th scope="col">Tên hiển thị (Caption)</th>
                                    <th scope="col">Là Admin</th>
                                    <th scope="col">Số quyền (Permissions)</th>
                                    <th scope="col">Ngày tạo</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="chk_child" value="{{ $role->id }}">
                                        </div>
                                    </th>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td><span class="fw-medium">{{ $role->caption }}</span></td>
                                    <td>
                                        @if($role->is_admin)
                                            <span class="badge bg-danger">Có</span>
                                        @else
                                            <span class="badge bg-light text-dark">Không</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info">{{ $role->permissions_count }}</span></td>
                                    <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('roles.update')
                                            <div class="edit">
                                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary edit-item-btn"><i class="ri-edit-2-line"></i> Chỉnh sửa</a>
                                            </div>
                                            @endcan
                                            @can('roles.delete')
                                            <div class="remove">
                                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vai trò này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger remove-item-btn"><i class="ri-delete-bin-line"></i> Xoá</button>
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
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->
@endsection

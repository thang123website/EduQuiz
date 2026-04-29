@extends('admin.layouts.master')

@section('title', 'Thêm danh mục Blog')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Thêm danh mục Blog</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.blog-categories.index') }}">Danh mục</a></li>
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
        <div class="col-lg-6 offset-lg-3">
            <form action="{{ route('admin.blog-categories.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin danh mục</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="VD: Tin tức, Hướng dẫn..." required>
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-muted fs-12">(Để trống để tự tạo từ tên)</span></label>
                            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}" placeholder="vd: tin-tuc, huong-dan...">
                            <div class="form-text">Chỉ dùng chữ thường, số và dấu gạch ngang. Slug sẽ được tạo tự động nếu để trống.</div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-light me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-add-line me-1"></i> Thêm mới
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

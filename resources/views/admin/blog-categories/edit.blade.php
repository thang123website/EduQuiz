@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa danh mục Blog')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Chỉnh sửa danh mục Blog</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.blog-categories.index') }}">Danh mục</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
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
            <form action="{{ route('admin.blog-categories.update', $blogCategory) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin danh mục</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $blogCategory->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $blogCategory->slug) }}">
                            <div class="form-text">Để trống để tự tạo lại từ tên mới.</div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-light me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line me-1"></i> Cập nhật
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

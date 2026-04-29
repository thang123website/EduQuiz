@extends('admin.layouts.master')

@section('title', 'Danh mục Blog')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Danh mục Blog</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Blog</a></li>
                        <li class="breadcrumb-item active">Danh mục</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách danh mục</h4>
                    @can('blog_category.create')
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="ri-add-line align-bottom me-1"></i> Thêm mới
                        </a>
                    </div>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">#</th>
                                    <th scope="col">Tên danh mục</th>
                                    <th scope="col">Slug</th>
                                    <th scope="col">Số bài viết</th>
                                    <th scope="col">Ngày tạo</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-medium">{{ $category->title }}</td>
                                    <td><code class="text-primary">{{ $category->slug }}</code></td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $category->blogs_count }} bài viết</span>
                                    </td>
                                    <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('blog_category.update')
                                            <a href="{{ route('admin.blog-categories.edit', $category) }}" class="btn btn-sm btn-primary">
                                                <i class="ri-edit-2-line"></i> Chỉnh sửa
                                            </a>
                                            @endcan
                                            @can('blog_category.delete')
                                            <form action="{{ route('admin.blog-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Xóa danh mục này? Các bài viết thuộc danh mục sẽ không bị xóa.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="ri-delete-bin-line"></i> Xóa
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="ri-inbox-line fs-24 d-block mb-2"></i>
                                        Chưa có danh mục nào
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

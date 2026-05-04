@extends('admin.layouts.master')

@section('title', 'Quản lý Bài viết')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý Bài viết</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Blog</a></li>
                        <li class="breadcrumb-item active">Bài viết</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách bài viết</h4>
                    @can('blog.create')
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="ri-add-line align-bottom me-1"></i> Thêm mới
                        </a>
                    </div>
                    @endcan
                </div>

                <div class="card-body border-bottom-dashed border-bottom">
                    <form action="{{ route('admin.blog.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xl-3">
                                <div class="search-box">
                                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tiêu đề..." value="{{ request('search') }}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div>
                                    <select class="form-control" name="category_id">
                                        <option value="">Tất cả danh mục</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ str_repeat('— ', $category->level) . $category->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div>
                                    <select class="form-control" name="status">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="publish" {{ request('status') == 'publish' ? 'selected' : '' }}>Đã xuất bản</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div>
                                    <input type="text" name="date" class="form-control" data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true" placeholder="Chọn khoảng ngày" value="{{ request('date') }}">
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ri-equalizer-fill me-1 align-bottom"></i> Hiển thị kết quả
                                    </button>
                                    @if(request()->anyFilled(['search', 'category_id', 'status', 'date']))
                                        <a href="{{ route('admin.blog.index') }}" class="btn btn-light w-100">Xoá</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 70px;">Ảnh</th>
                                    <th scope="col">Tiêu đề</th>
                                    <th scope="col">Danh mục</th>
                                    <th scope="col">Tác giả</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Lượt xem</th>
                                    <th scope="col">Ngày tạo</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blogs as $blog)
                                <tr>
                                    <td>
                                        @if($blog->image_url)
                                            <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="rounded" style="width: 60px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 45px;">
                                                <i class="ri-image-line text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ Str::limit($blog->title, 50) }}</div>
                                        <small class="text-muted"><code>{{ $blog->slug }}</code></small>
                                    </td>
                                    <td>
                                        @if($blog->category)
                                            <span class="badge bg-primary-subtle text-primary">{{ $blog->category->title }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $blog->author?->name ?? '—' }}</td>
                                    <td>
                                        @if($blog->status === 'publish')
                                            <span class="badge bg-success-subtle text-success">Đã xuất bản</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Chờ duyệt</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info-subtle text-info">{{ number_format($blog->visit_count) }}</span></td>
                                    <td>{{ $blog->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('blog.update')
                                            <a href="{{ route('admin.blog.edit', $blog) }}" class="btn btn-sm btn-primary">
                                                <i class="ri-edit-2-line"></i>
                                            </a>
                                            @endcan
                                            @can('blog.delete')
                                            <form action="{{ route('admin.blog.destroy', $blog) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger confirm-delete">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="ri-article-line fs-24 d-block mb-2"></i>
                                        Chưa có bài viết nào
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $blogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layouts.master')

@section('title', 'Quản lý Thẻ (Tags)')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quản lý Thẻ (Tags)</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Quản lý Thẻ</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Danh sách thẻ</h4>
                <div class="flex-shrink-0">
                    <form action="{{ route('admin.tags.index') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm thẻ..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên thẻ</th>
                                <th>Slug</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tags as $tag)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge bg-info">{{ $tag->name }}</span></td>
                                    <td>{{ $tag->slug }}</td>
                                    <td>{{ $tag->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-soft-primary edit-item" data-item="{{ json_encode($tag) }}">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thẻ này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Chưa có thẻ nào được tạo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $tags->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0" id="form-title">Thêm thẻ mới</h4>
            </div>
            <div class="card-body">
                <form id="tag-form" action="{{ route('admin.tags.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="id" id="tag-id">

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thẻ (Tag) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Ví dụ: 2024, New economy..." required>
                        <small class="text-muted">Tên thẻ dùng để lọc các đề thi theo năm, bộ sách, v.v.</small>
                    </div>

                    <div class="text-end">
                        <button type="button" id="btn-reset" class="btn btn-light me-1 d-none">Hủy bỏ</button>
                        <button type="submit" class="btn btn-primary">Lưu thông tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Edit Click
        document.querySelectorAll('.edit-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const data = JSON.parse(this.dataset.item);
                
                document.getElementById('form-title').innerText = 'Chỉnh sửa thẻ: ' + data.name;
                document.getElementById('form-method').value = 'PUT';
                document.getElementById('tag-form').action = PATH_ROOT + '/admin/tags/' + data.id;
                
                document.getElementById('tag-id').value = data.id;
                document.getElementById('name').value = data.name;
                
                document.getElementById('btn-reset').classList.remove('d-none');
            });
        });

        // Handle Reset
        document.getElementById('btn-reset').addEventListener('click', function() {
            document.getElementById('tag-form').reset();
            document.getElementById('form-title').innerText = 'Thêm thẻ mới';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('tag-form').action = "{{ route('admin.tags.store') }}";
            this.classList.add('d-none');
        });
    });
</script>
@endpush

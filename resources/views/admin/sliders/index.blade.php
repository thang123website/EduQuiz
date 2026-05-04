@extends('admin.layouts.master')

@section('title', 'Quản lý Slider')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý Slider</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Slider</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="sliderList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Danh sách Slider Group</h5>
                        @can('slider.create')
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.sliders.create') }}" class="btn btn-success add-btn">
                                <i class="ri-add-line align-bottom me-1"></i> Tạo Slider mới
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{ route('admin.sliders.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xxl-4 col-sm-6">
                                <div class="search-box">
                                    <input type="text" name="search" class="form-control search" placeholder="Tìm kiếm tên slider hoặc key..." value="{{ request('search') }}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-6">
                                <div>
                                    <select class="form-control" name="status">
                                        <option value="">Trạng thái</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tắt</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-3 col-sm-6">
                                <div>
                                    <input type="text" name="date" class="form-control" data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true" placeholder="Chọn khoảng ngày" value="{{ request('date') }}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ri-equalizer-fill me-1 align-bottom"></i> Hiển thị kết quả
                                    </button>
                                    @if(request()->anyFilled(['search', 'status', 'date']))
                                        <a href="{{ route('admin.sliders.index') }}" class="btn btn-light w-100">Xoá lọc</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive table-card mb-1">
                            <table class="table align-middle" id="sliderTable">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th class="sort" data-sort="slider_name">Tên Slider</th>
                                        <th class="sort" data-sort="slider_key">Key định danh</th>
                                        <th class="sort text-center" data-sort="slider_count">Số Slide</th>
                                        <th class="sort" data-sort="slider_status">Trạng thái</th>
                                        <th class="sort" data-sort="slider_date">Ngày tạo</th>
                                        <th class="sort" data-sort="action">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @forelse($sliders as $slider)
                                    <tr>
                                        <td class="slider_name">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h5 class="fs-14 mb-1"><a href="{{ route('admin.sliders.edit', $slider) }}" class="text-dark">{{ $slider->name }}</a></h5>
                                                    @if($slider->description)
                                                        <p class="text-muted mb-0">{{ Str::limit($slider->description, 60) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="slider_key">
                                            <span class="badge bg-light text-primary"><code>{{ $slider->key }}</code></span>
                                        </td>
                                        <td class="slider_count text-center">
                                            <span class="badge bg-info-subtle text-info fs-12">{{ $slider->items_count }} items</span>
                                        </td>
                                        <td class="slider_status">
                                            @if($slider->status === 'active')
                                                <span class="badge bg-success-subtle text-success text-uppercase">Hoạt động</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger text-uppercase">Tắt</span>
                                            @endif
                                        </td>
                                        <td class="slider_date">{{ $slider->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                @can('slider.update')
                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Chỉnh sửa">
                                                    <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-primary d-inline-block">
                                                        <i class="ri-edit-2-line fs-16"></i>
                                                    </a>
                                                </li>
                                                @endcan
                                                @can('slider.delete')
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xóa">
                                                    <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <a class="text-danger d-inline-block confirm-delete" href="javascript:void(0);">
                                                            <i class="ri-delete-bin-line fs-16"></i>
                                                        </a>
                                                    </form>
                                                </li>
                                                @endcan
                                            </ul>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="noresult" style="display: block;">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Xin lỗi! Không tìm thấy kết quả</h5>
                                                    <p class="text-muted mb-0">Chúng tôi không tìm thấy bất kỳ slider nào trong hệ thống.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            <div class="pagination-wrap hstack gap-2">
                                {{ $sliders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection

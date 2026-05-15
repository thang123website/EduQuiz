@extends('admin.layouts.master')

@section('title', 'Chi tiết Yêu cầu / Form #' . $form->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Chi tiết Form #{{ $form->id }}</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.forms.index') }}" class="btn btn-light"><i class="ri-arrow-left-line me-1"></i> Quay lại</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header border-bottom-dashed">
                <h5 class="card-title mb-0">Thông tin dữ liệu gửi lên</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-nowrap align-middle mb-0">
                    <tbody>
                        @foreach($form->data as $key => $value)
                        <tr>
                            <th class="ps-0" style="width: 30%;"><span class="text-muted text-uppercase">{{ str_replace('_', ' ', $key) }}</span></th>
                            <td class="text-wrap">
                                @if(is_array($value))
                                    <pre class="mb-0 bg-light p-2 rounded"><code>{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                @else
                                    <span class="fw-medium">{{ $value }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header border-bottom-dashed">
                <h5 class="card-title mb-0">Xử lý trạng thái</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.forms.update-status', $form->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Tình trạng xử lý</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $form->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ $form->status == 'processing' ? 'selected' : '' }}>Đang giải quyết</option>
                            <option value="resolved" {{ $form->status == 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
                            <option value="ignored" {{ $form->status == 'ignored' ? 'selected' : '' }}>Bỏ qua</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
                </form>

                <hr class="my-4 dashed">

                <h6 class="text-uppercase fw-semibold mb-3">Thông tin hệ thống</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th>Phân loại:</th>
                                <td><span class="badge bg-info-subtle text-info text-uppercase">{{ $form->type }}</span></td>
                            </tr>
                            <tr>
                                <th>Tài khoản:</th>
                                <td>
                                    @if($form->user)
                                        <a href="{{ route('admin.users.show', $form->user->id) }}">{{ $form->user->name }}</a>
                                    @else
                                        <span class="text-muted">Khách (Guest)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>IP Gửi:</th>
                                <td>{{ $form->ip_address }}</td>
                            </tr>
                            <tr>
                                <th>Ngày gửi:</th>
                                <td>{{ display_datetime($form->created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

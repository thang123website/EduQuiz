@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa người dùng')

@section('content')
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg profile-setting-img">
                <img src="{{ $user->cover_photo ? (str_starts_with($user->cover_photo, 'http') ? $user->cover_photo : Storage::url($user->cover_photo)) : asset('assets/images/profile-bg.jpg') }}" class="profile-wid-img" alt="" id="cover-img-preview">
                <div class="overlay-content">
                    <div class="text-end p-3">
                        <div class="p-0 ms-auto rounded-circle profile-photo-edit">
                            <input id="cover_photo" type="hidden" name="cover_photo" value="{{ $user->cover_photo }}">
                            <button type="button" class="btn btn-light" onclick="openMediaPicker('cover_photo', 'cover-img-preview')">
                                <i class="ri-image-edit-line align-bottom me-1"></i> Đổi ảnh bìa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xxl-3">
                <div class="card mt-n5">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                                <img src="{{ $user->avatar_url }}" 
                                     class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image" id="avatar-img-preview">
                                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                    <input id="avatar" type="hidden" name="avatar" value="{{ $user->avatar }}">
                                    <button type="button" class="avatar-title rounded-circle bg-light text-body border-0" onclick="openMediaPicker('avatar', 'avatar-img-preview')">
                                        <i class="ri-camera-fill"></i>
                                    </button>
                                </div>
                            </div>
                            <h5 class="fs-16 mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">{{ $user->role ? $user->role->caption : 'Thành viên' }}</p>
                        </div>
                    </div>
                </div>
                <!--end card-->
            </div>
            <!--end col-->
            <div class="col-xxl-9">
                <div class="card mt-xxl-n5">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                    <i class="fas fa-home"></i> Thông tin cơ bản
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                    <i class="far fa-user"></i> Đổi mật khẩu
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <!-- Thông tin cơ bản -->
                            <div class="tab-pane active" id="personalDetails" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Nhập họ và tên" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="example@gmail.com" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="gender">Giới tính</label>
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">Chọn giới tính...</option>
                                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="dob" class="form-label">Ngày sinh</label>
                                            <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob', $user->dob) }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Địa chỉ</label>
                                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address) }}" placeholder="Nhập địa chỉ">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="latitude" class="form-label">Latitude (Vĩ độ)</label>
                                            <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $user->latitude) }}" placeholder="Ví dụ: 21.0285">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="longitude" class="form-label">Longitude (Kinh độ)</label>
                                            <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $user->longitude) }}" placeholder="Ví dụ: 105.8542">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="role_id">Vai trò (Phân quyền)</label>
                                            <select class="form-select" id="role_id" name="role_id">
                                                <option value="">Chọn vai trò...</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->caption }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="account_status">Trạng thái tài khoản</label>
                                            <select class="form-select" id="account_status" name="status">
                                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                                <option value="blocked" {{ old('status', $user->status) == 'blocked' ? 'selected' : '' }}>Bị chặn</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-end mt-3">
                                            <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-soft-danger">Hủy bỏ</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end tab-pane-->

                            <!-- Đổi mật khẩu -->
                            <div class="tab-pane" id="changePassword" role="tabpanel">
                                <div class="row g-2">
                                    <div class="col-lg-6">
                                        <div>
                                            <label for="password" class="form-label">Mật khẩu mới</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Để trống nếu không đổi">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div>
                                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Xác nhận lại mật khẩu">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mt-3">
                                            <div class="alert alert-warning">
                                                Lưu ý: Nếu bạn nhập mật khẩu mới, mật khẩu cũ của người dùng sẽ bị thay đổi ngay lập tức.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-end mt-3">
                                            <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-soft-danger">Hủy bỏ</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end tab-pane-->
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
    </form>

    {{-- Tích hợp Media Manager --}}
    @include('admin.media.picker-modal')

    <script>
        function openMediaPicker(inputId, previewId) {
            window.mediaPickerTarget = {
                input: inputId,
                preview: previewId
            };
            $('#mediaModal').modal('show');
        }

        // Lắng nghe sự kiện chọn ảnh từ Media Manager
        window.addEventListener('media-selected', function(e) {
            const { input, preview } = window.mediaPickerTarget;
            const path = e.detail.path;
            
            // Cập nhật giá trị input
            document.getElementById(input).value = path;
            
            // Cập nhật ảnh preview
            const previewElem = document.getElementById(preview);
            if (previewElem) {
                // Nếu path bắt đầu bằng http thì dùng luôn, ngược lại thêm /storage/
                const fullUrl = path.startsWith('http') ? path : '/storage/' + path.replace(/^\/storage\//, '');
                previewElem.src = fullUrl;
            }
        });
    </script>
@endsection

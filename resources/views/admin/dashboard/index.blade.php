@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Welcome to EduQuiz Admin</h4>
                </div>
                <div class="card-body">
                    <p>Hệ thống Giao diện Admin đã được thiết lập thành công theo chuẩn Senior.</p>
                    <x-admin.button-submit class="w-md">Test Loading Button</x-admin.button-submit>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const btnLoad = document.querySelector('.btn-load');
    if (btnLoad) {
        btnLoad.addEventListener('click', function() {
            let btn = this;
            btn.disabled = true;
            btn.querySelector('.spinner-border').classList.remove('d-none');
            setTimeout(() => {
                btn.disabled = false;
                btn.querySelector('.spinner-border').classList.add('d-none');
            }, 2000);
        });
    }
</script>
@endpush

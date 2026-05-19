@extends('admin.layouts.master')

@section('title', 'Cấu hình API')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Cấu hình API</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hệ thống</a></li>
                    <li class="breadcrumb-item active">Cấu hình API</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <h5 class="fs-14 mb-1">API settings</h5>
        <p class="text-muted">Configure your API access and security settings</p>
    </div>
    
    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success alert-border-left alert-dismissible fade show mb-4" role="alert">
                <i class="ri-check-double-line me-3 align-middle"></i> <strong>Thành công</strong> - {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_group" value="api">

                    <div class="mb-4">
                        <div class="form-check form-switch form-switch-md mb-2">
                            <input type="hidden" name="api_enabled" value="0">
                            <input type="checkbox" class="form-check-input" id="api_enabled" name="api_enabled" value="1" {{ \App\Models\Setting::get('api_enabled') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="api_enabled">Enable API</label>
                        </div>
                        <p class="text-muted fs-13 mb-0">Enable or disable the REST API for your website. When disabled, all API endpoints will be inaccessible.</p>
                    </div>

                    <div class="border rounded p-3 mb-4 bg-light">
                        <h6 class="fs-13 fw-bold mb-1">Security Settings</h6>
                        <p class="text-muted fs-13 mb-3">The API key acts as a shared secret between your server and clients (e.g., your mobile app). It prevents unknown clients from accessing the API. User identity is still handled separately by Sanctum tokens.</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">API Key</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="api_key" id="api_key" value="{{ \App\Models\Setting::get('api_key') }}" readonly>
                                <button class="btn btn-outline-primary" type="button" onclick="generateApiKey()">
                                    <i class="ri-refresh-line align-middle me-1"></i> Generate Random Key
                                </button>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey()">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                            <div class="mt-2 text-muted fs-13">Optional security key for API access. When set, all API requests must include this key in the X-API-KEY header.</div>
                            
                            @if(\App\Models\Setting::get('api_key'))
                                <div class="mt-2 text-success fs-13 fw-medium">
                                    <i class="ri-shield-check-line align-middle me-1"></i> API key protection is enabled. All requests require the X-API-KEY header.
                                </div>
                            @else
                                <div class="mt-2 text-warning fs-13 fw-medium">
                                    <i class="ri-error-warning-line align-middle me-1"></i> API key protection is disabled. Requests do not require an API key.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border rounded p-3 bg-light mb-4">
                        <h6 class="fs-13 fw-bold mb-1">API Caching Settings</h6>
                        <p class="text-muted fs-13 mb-3">Optimize API performance by caching responses. If disabled, every request will query the database directly.</p>
                        
                        <div class="form-check form-switch form-switch-md mb-3">
                            <input type="hidden" name="api_cache_enabled" value="0">
                            <input type="checkbox" class="form-check-input" id="api_cache_enabled" name="api_cache_enabled" value="1" {{ \App\Models\Setting::get('api_cache_enabled', 0) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="api_cache_enabled">Enable API Cache</label>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label fw-semibold" for="api_cache_duration">Cache Duration (seconds)</label>
                            <input type="number" class="form-control" id="api_cache_duration" name="api_cache_duration" value="{{ \App\Models\Setting::get('api_cache_duration', 3600) }}" min="1">
                            <div class="mt-1 text-muted fs-13">The number of seconds to store API responses in cache. Example: 3600 for 1 hour.</div>
                        </div>
                    </div>



                    <div class="border rounded p-3 bg-light mb-4">
                        <h6 class="fs-13 fw-bold mb-1">Push Notifications (FCM v1 API)</h6>
                        <p class="text-muted fs-13 mb-3">Send push notifications to your mobile app users via Firebase Cloud Messaging. This requires a mobile app that registers device tokens with your API.</p>
                        
                        <div class="form-check form-switch form-switch-md">
                            <input type="hidden" name="fcm_enabled" value="0">
                            <input type="checkbox" class="form-check-input" id="fcm_enabled" name="fcm_enabled" value="1" {{ \App\Models\Setting::get('fcm_enabled') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="fcm_enabled">Enable Push Notifications</label>
                        </div>
                        <p class="text-muted fs-13 mt-1 mb-0">Enable or disable push notifications for mobile apps. When disabled, no notifications will be sent to devices.</p>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line align-bottom me-1"></i> Save settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function generateApiKey() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let key = '';
        for (let i = 0; i < 32; i++) {
            key += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('api_key').value = key;
    }

    function copyApiKey() {
        const copyText = document.getElementById('api_key');
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        // Simple toast or alert can be added here
        alert("API Key copied: " + copyText.value);
    }
</script>
@endsection

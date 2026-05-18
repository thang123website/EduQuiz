@props(['model' => null, 'fields' => []])

@php
    $languages = get_active_languages();
@endphp

<ul class="nav nav-tabs nav-justified mb-3" role="tablist">
    @foreach($languages as $index => $lang)
    <li class="nav-item">
        <a class="nav-link {{ $index == 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#lang-{{ $lang['code'] }}" role="tab">
            {{ $lang['name'] }}
        </a>
    </li>
    @endforeach
</ul>

<div class="tab-content text-muted border p-3 rounded">
    @foreach($languages as $index => $lang)
    <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="lang-{{ $lang['code'] }}" role="tabpanel">
        
        @foreach($fields as $fieldName => $config)
            @php
                $type = $config['type'] ?? 'text';
                $label = $config['label'] ?? ucfirst($fieldName);
                $placeholder = $config['placeholder'] ?? '';
                $required = ($config['required'] ?? false) && $index == 0; // Only require default lang
                $rows = $config['rows'] ?? 3;
                
                // Get value safely
                $value = '';
                if ($model && method_exists($model, 'getTranslation')) {
                    $value = old("{$fieldName}." . $lang['code'], $model->getTranslation($fieldName, $lang['code'], false));
                } else {
                    $value = old("{$fieldName}." . $lang['code']);
                }
            @endphp

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    {{ $label }} ({{ $lang['code'] }})
                    @if($required) <span class="text-danger">*</span> @endif
                </label>

                @if($type === 'textarea')
                    <textarea class="form-control {{ $config['class'] ?? '' }}" name="{{ $fieldName }}[{{ $lang['code'] }}]" rows="{{ $rows }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>{{ $value }}</textarea>
                @else
                    <input type="{{ $type }}" class="form-control" name="{{ $fieldName }}[{{ $lang['code'] }}]" value="{{ $value }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>
                @endif
            </div>
        @endforeach

    </div>
    @endforeach
</div>

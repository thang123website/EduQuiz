<button type="submit" {{ $attributes->merge(['class' => 'btn btn-primary btn-load']) }}>
    <span class="d-flex align-items-center">
        <span class="flex-grow-1 me-2">{{ $slot }}</span>
        <span class="spinner-border flex-shrink-0 d-none" role="status">
            <span class="visually-hidden">Loading...</span>
        </span>
    </span>
</button>

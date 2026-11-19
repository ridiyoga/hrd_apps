@php
    $upper = $upper ?? false;
@endphp

<div class="form-input {{ $class }}">
    <span class="input-icon">
        <i class="{{ $icon }}"></i>
    </span>

    <input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="{{ $name }}" value="{{ $value ?? '' }}"
        placeholder="{{ $label }}" @isset($accept) accept="{{ $accept }}" @endisset
        class="form-control @error($name) is-invalid @enderror"
        @if ($upper) style="text-transform:uppercase" @endif>
    @error($name)
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

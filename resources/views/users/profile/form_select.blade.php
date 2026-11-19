@php
$type = $type ?? 'select';
@endphp

<div class="mb-3 form-input {{ $class }}">
    <span class="input-icon">
        <i class=" {{ $icon }}"></i>
    </span>

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-control {{$class}} @error($name) is-invalid @enderror">

        <option value="" selected>
            {{ $label }}
        </option>

        @foreach($options as $key => $text)
        <option value="{{ $key }}"
            {{ old($name, $value ?? '') == $key ? 'selected' : '' }}>
            {{ $text }}
        </option>
        @endforeach
    </select>
</div>

@error($name)
<p class="text-danger small mt-1">{{ $message }}</p>
@enderror
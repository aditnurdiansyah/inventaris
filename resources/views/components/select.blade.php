@props(['name', 'label', 'required' => true, 'hint' => null])
<div class="field">
    <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="req" aria-hidden="true">*</span>@endif</label>
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->class(['input', 'is-invalid' => $errors->has($name)]) }}>
        {{ $slot }}
    </select>
    <p class="hint" data-hint>{{ $hint }}</p>
    @error($name)<p class="field-error">{{ $message }}</p>@enderror
</div>

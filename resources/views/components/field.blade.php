@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => true, 'hint' => null])
<div class="field">
    <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="req" aria-hidden="true">*</span>@endif</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" {{ $attributes->class(['input', 'is-invalid' => $errors->has($name)]) }}>
    @if ($hint)<p class="hint">{{ $hint }}</p>@endif
    @error($name)<p class="field-error">{{ $message }}</p>@enderror
</div>

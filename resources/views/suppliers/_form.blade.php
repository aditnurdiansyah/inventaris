@php
    $supplier = $supplier ?? null;
    $isEdit = $supplier && $supplier->exists;
@endphp

<form method="POST" action="{{ $isEdit ? route('suppliers.update', $supplier) : route('suppliers.store') }}" class="card form-card" novalidate>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <x-field name="name" label="Nama Pemasok" :value="$supplier?->name" maxlength="150" />
    <x-field name="phone" label="Telepon" :value="$supplier?->phone" :required="false" maxlength="20" />
    <x-field name="email" label="Email" type="email" :value="$supplier?->email" :required="false" maxlength="100" />

    <div class="field">
        <label for="address" class="label">Alamat</label>
        <textarea id="address" name="address" rows="3" maxlength="500" @class(['input', 'is-invalid' => $errors->has('address')]) style="height:auto;padding:var(--space-3)">{{ old('address', $supplier?->address) }}</textarea>
        @error('address')<p class="field-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>

{{--
    Partial: partials/form-field.blade.php
    Variables:
      $field    — DocumentField model instance
      $docType  — DocumentType model instance
      $fields   — full collection of fields for this docType (needed for repeating_group children)
--}}

@php
    $inputClass = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
    $inputName = "field_{$field->field_key}";
    $oldValue = old($inputName, '');
    $required = $field->is_required ? 'required' : '';
@endphp

@if (in_array($field->field_type, ['staff_loop', 'official_loop']))
        @php $loopType = $field->field_type === 'staff_loop' ? 'staff' : 'official'; @endphp

        <div class="border border-gray-200 rounded-lg overflow-hidden"
             data-loop-type="{{ $loopType }}"
             data-field-key="{{ $field->field_key }}"
             data-doc-key="{{ $docType->key }}">

            {{-- Header --}}
            <div class="bg-gray-50 px-3 py-2 border-b flex items-center justify-between">
                <label class="block text-sm font-medium text-gray-700">
                    {{ $field->label }}
                    @if ($field->is_required)<span class="text-red-500">*</span>@endif
                </label>
                <span class="text-xs text-gray-400">Centang dan drag untuk mengurutkan</span>
            </div>

            {{-- Search --}}
            <div class="px-3 py-2 border-b">
                <input type="text"
                    class="loop-search w-full border border-gray-200 rounded px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                    placeholder="Cari nama..." />
            </div>

            {{-- Scrollable checklist --}}
            <div class="loop-checklist max-h-48 overflow-y-auto divide-y divide-gray-50 px-1 py-1">
                {{-- Populated by JS --}}
            </div>

            {{-- Selected order preview --}}
            <div class="px-3 py-2 bg-blue-50 border-t text-xs text-blue-600">
                Urutan item yang dicentang akan digunakan dalam dokumen.
                Drag ⠿ untuk mengubah urutan.
            </div>
        </div>

@elseif ($field->field_type === 'repeating_group')
        @php
            $children = $fields->where('is_group_child', true)->where('group_key', $field->field_key);
        @endphp
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">
                    {{ $field->label }}
                    @if ($field->is_required)<span class="text-red-500">*</span>@endif
                </label>
                <button type="button"
                    onclick="addRow('{{ $docType->key }}', '{{ $field->field_key }}')"
                    class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                    + Tambah Baris
                </button>
            </div>
            {{-- Column headers --}}
            <div class="grid gap-2 mb-1"
                 style="grid-template-columns: repeat({{ $children->count() }}, 1fr) auto">
                @foreach ($children as $child)
                    <span class="text-xs font-medium text-gray-500">{{ $child->label }}</span>
                @endforeach
                <span></span>
            </div>
            <div id="rows-{{ $docType->key }}-{{ $field->field_key }}"></div>
            <template id="row-template-{{ $docType->key }}-{{ $field->field_key }}">
                <div class="grid gap-2 mb-2 row-item"
                     style="grid-template-columns: repeat({{ $children->count() }}, 1fr) auto">
                    @foreach ($children as $child)
                        <input type="text"
                            name="field_{{ $field->field_key }}[__INDEX__][{{ $child->field_key }}]"
                            placeholder="{{ $child->label }}"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            {{ $child->is_required ? 'required' : '' }} />
                    @endforeach
                    <button type="button" onclick="removeRow(this)"
                        class="text-red-400 hover:text-red-600 text-lg font-bold px-2">×</button>
                </div>
            </template>
        </div>

@else
    <div data-doctype="{{ $docType->key }}"
         data-field-key="{{ $field->field_key }}"
         data-autofill-col="{{ $field->staff_autofill_column ?? '' }}"
         data-autofill-role="{{ $field->autofill_role ?? 'none' }}">

        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $field->label }}
            @if ($field->is_required)<span class="text-red-500">*</span>@endif
        </label>

        @if ($field->field_type === 'textarea')
            <textarea name="{{ $inputName }}" rows="3"
                class="{{ $inputClass }}" {{ $required }}>{{ $oldValue }}</textarea>

        @elseif ($field->field_type === 'date')
            <input type="date" name="{{ $inputName }}"
                value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }} />

        @elseif ($field->field_type === 'number')
            <input type="number" name="{{ $inputName }}"
                value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }} />

        @elseif ($field->field_type === 'select')
            <select name="{{ $inputName }}" class="{{ $inputClass }}" {{ $required }}>
                <option value="">— Pilih —</option>
                @foreach ($field->field_options ?? [] as $option)
                    <option value="{{ $option }}" {{ $oldValue === $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>

        @elseif ($field->field_type === 'checkbox')
            <div class="flex items-center gap-2 mt-1">
                <input type="checkbox" name="{{ $inputName }}"
                    value="1" id="cb-{{ $field->field_key }}-{{ $docType->key }}"
                    {{ $oldValue ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600" />
                <label for="cb-{{ $field->field_key }}-{{ $docType->key }}"
                    class="text-sm text-gray-600">{{ $field->label }}</label>
            </div>

        @else
            <input type="text" name="{{ $inputName }}"
                value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }}
                placeholder="{{ $field->label }}..." />
        @endif
    </div>
@endif
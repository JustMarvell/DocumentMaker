{{--
    Partial: partials/form-field.blade.php
    Variables:
      $field    — DocumentField model instance
      $docType  — DocumentType model instance
      $fields   — full collection of fields for this docType (needed for repeating_group children)
--}}

@php
    $inputClass = 'w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors';
    $inputName  = "field_{$field->field_key}";
    $oldValue   = old($inputName, '');
    $required   = $field->is_required ? 'required' : '';
@endphp

{{-- ============================================================ --}}
{{-- Staff loop / Official loop                                   --}}
{{-- ============================================================ --}}
@if (in_array($field->field_type, ['staff_loop', 'official_loop']))
    @php $loopType = $field->field_type === 'staff_loop' ? 'staff' : 'official'; @endphp

    <div class="border border-gray-200 rounded-lg overflow-hidden"
         data-loop-type="{{ $loopType }}"
         data-field-key="{{ $field->field_key }}"
         data-doc-key="{{ $docType->key }}">

        {{-- Header --}}
        <div class="bg-gray-50 px-3 py-2.5 border-b flex items-center justify-between">
            <div class="flex items-center gap-2 min-w-0">
                <label class="block text-sm font-medium text-gray-700 truncate">
                    {{ $field->label }}
                    @if ($field->is_required)<span class="text-red-500">*</span>@endif
                </label>
                <span class="loop-count hidden bg-blue-600 text-white text-xs font-semibold px-2 py-0.5 rounded-full flex-shrink-0 transition-all"></span>
            </div>
            <span class="text-xs text-gray-400 flex-shrink-0 ml-2 hidden sm:block">Centang &amp; drag ⠿</span>
        </div>

        {{-- Search --}}
        <div class="px-3 py-2 border-b">
            <input type="text"
                class="loop-search w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"
                placeholder="Cari nama..." />
        </div>

        {{-- Scrollable checklist — taller on mobile for easier touch --}}
        <div class="loop-checklist overflow-y-auto divide-y divide-gray-50 px-1 py-1"
             style="max-height: 220px;">
            {{-- Populated by JS --}}
        </div>

        {{-- Footer tip --}}
        <div class="px-3 py-2 bg-blue-50 border-t flex items-center justify-between">
            <span class="text-xs text-blue-600">Urutan item yang dicentang = urutan dalam dokumen.</span>
            <span class="text-xs text-blue-400 hidden sm:block">Drag ⠿ untuk mengurutkan</span>
        </div>
    </div>

{{-- ============================================================ --}}
{{-- Repeating group                                              --}}
{{-- ============================================================ --}}
@elseif ($field->field_type === 'repeating_group')
    @php
        $children  = $fields->where('is_group_child', true)->where('group_key', $field->field_key);
        $childCount = $children->count();
    @endphp

    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-gray-700">
                {{ $field->label }}
                @if ($field->is_required)<span class="text-red-500">*</span>@endif
            </label>
            <button type="button"
                onclick="addRow('{{ $docType->key }}', '{{ $field->field_key }}')"
                class="flex-shrink-0 text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition">
                + Tambah Baris
            </button>
        </div>

        {{-- Column headers — hidden on mobile (labels are on inputs instead) --}}
        @if ($childCount > 0)
            <div class="hidden sm:grid gap-2 mb-1"
                 style="grid-template-columns: repeat({{ $childCount }}, 1fr) auto">
                @foreach ($children as $child)
                    <span class="text-xs font-medium text-gray-500">{{ $child->label }}</span>
                @endforeach
                <span></span>
            </div>
        @endif

        <div id="rows-{{ $docType->key }}-{{ $field->field_key }}"></div>

        <template id="row-template-{{ $docType->key }}-{{ $field->field_key }}">
            {{-- Mobile: single column stack; Desktop: multi-column grid --}}
            <div class="row-item mb-3 p-3 border border-gray-200 rounded-lg sm:p-0 sm:border-0 sm:rounded-none sm:mb-2">
                <div class="grid gap-2 sm:gap-2"
                     style="grid-template-columns: 1fr auto"
                     data-desktop-cols="{{ $childCount }}">

                    {{-- On mobile all inputs stack; we show label as placeholder --}}
                    <div class="grid gap-2 row-group-grid"
                         style="grid-template-columns: repeat({{ $childCount }}, 1fr)">
                        @foreach ($children as $child)
                            <input type="text"
                                name="field_{{ $field->field_key }}[__INDEX__][{{ $child->field_key }}]"
                                placeholder="{{ $child->label }}"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                                {{ $child->is_required ? 'required' : '' }} />
                        @endforeach
                    </div>

                    <button type="button" onclick="removeRow(this)"
                        class="self-start text-red-400 hover:text-red-600 text-xl font-bold px-2 py-1 leading-none">
                        ×
                    </button>
                </div>
            </div>
        </template>
    </div>

{{-- ============================================================ --}}
{{-- Regular fields                                               --}}
{{-- ============================================================ --}}
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
            <div class="flex items-center gap-3 mt-1 p-3 border border-gray-200 rounded-lg bg-gray-50">
                <input type="checkbox" name="{{ $inputName }}"
                    value="1" id="cb-{{ $field->field_key }}-{{ $docType->key }}"
                    {{ $oldValue ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-300 text-blue-600 flex-shrink-0" />
                <label for="cb-{{ $field->field_key }}-{{ $docType->key }}"
                    class="text-sm text-gray-600 cursor-pointer select-none">
                    {{ $field->label }}
                </label>
            </div>

        @else
            {{-- Default: text --}}
            <input type="text" name="{{ $inputName }}"
                value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }}
                placeholder="{{ $field->label }}..." />
        @endif
    </div>
@endif
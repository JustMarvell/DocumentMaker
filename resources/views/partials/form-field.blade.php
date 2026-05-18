{{--
    Partial: partials/form-field.blade.php
    Variables: $field, $docType, $fields
--}}
@php
$inputClass = 'w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2
    focus:ring-blue-500 transition-colors';
$key = "field_{$field->field_key}";
$label = $field->label;
$required = $field->is_required;
$type = $field->field_type;
$old = old($key);
$icon = $field->icon ?? null;
$inputWithIconClass = $icon ? 'w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
    transition-colors' : $inputClass;

$isAutoNumber = isset($autoNumberField) && $autoNumberField === $field->field_key;
$isAutofillable = !$isAutoNumber && $field->staff_autofill_column && $field->autofill_role !== 'none';
@endphp

<div data-field-key="{{ $field->field_key }}" class="field-wrapper">

    {{-- ── Label ─────────────────────────────────────────────── --}}
    @if(!in_array($type, ['loop_staff', 'loop_official', 'repeating_group', 'checkbox', 'heading']))
        <label class="form-label" for="{{ $key }}">
            @if ($icon)
                <i class="{{ $icon }} text-gray-400 text-sm w-4 text-center flex-shrink-0"></i>
            @endif
            {{ $label }}
            @if($required)
                <span style="color:#dc2626;margin-left:0.15rem;">*</span>
            @endif
            @if($isAutoNumber) {{-- <- add badge --}} <span style="
                            display:inline-flex;align-items:center;gap:0.25rem;
                            margin-left:0.4rem;padding:0.1rem 0.45rem;
                            background:rgba(124,58,237,0.1);border:1px solid rgba(124,58,237,0.25);
                            border-radius:20px;font-size:0.62rem;font-weight:600;
                            color:#6d28d9;letter-spacing:0.03em;vertical-align:middle;">
                <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                </svg>
                Otomatis
                </span>
            @elseif($isAutofillable) {{-- <- add badge --}} <span style="
                            display:inline-flex;align-items:center;gap:0.25rem;
                            margin-left:0.4rem;padding:0.1rem 0.45rem;
                            background:rgba(42,82,152,0.08);border:1px solid rgba(42,82,152,0.2);
                            border-radius:20px;font-size:0.62rem;font-weight:600;
                            color:var(--navy-600);letter-spacing:0.03em;vertical-align:middle;">
                    <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Autofill
                    </span>
            @endif
            @if($field->helper_text)
                <span style="font-weight:400;color:var(--slate-400);font-size:0.72rem;margin-left:0.35rem;">
                    — {{ $field->helper_text }}
                </span>
            @endif
        </label>
    @endif

    {{-- ── TEXT ────────────────────────────────────────────────── --}}
    @if($type === 'text')
        <div style="position:relative;">
            <input type="text"
                id="{{ $key }}"
                name="{{ $key }}"
                value="{{ $old ?? '' }}"
                class="form-input"
                placeholder="{{ $isAutoNumber ? 'Digenerate otomatis...' : ($field->placeholder ?? '') }}"
                data-required="{{ $required ? '1' : '0' }}"
                data-label="{{ $label }}"
                {{ $required ? 'required' : '' }}
                {{ $field->maxlength ? "maxlength={$field->maxlength}" : '' }}
                @if($isAutoNumber)
                    readonly
                    style="background:rgba(124,58,237,0.04);border-color:rgba(124,58,237,0.25);
                            color:rgba(109,40,217,0.6);cursor:not-allowed;padding-right:2.5rem;"
                @endif
            >
            @if($isAutoNumber)
                <div style="position:absolute;right:0.65rem;top:50%;transform:translateY(-50%);
                            color:rgba(124,58,237,0.4);pointer-events:none;">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            @endif
        </div>

    {{-- ── TEXTAREA ────────────────────────────────────────────── --}}
    @elseif($type === 'textarea')
        <textarea id="{{ $key }}"
            name="{{ $key }}"
            class="form-input"
            rows="{{ $field->rows ?? 3 }}"
            style="resize:vertical;"
            placeholder="{{ $field->placeholder ?? '' }}"
            data-required="{{ $required ? '1' : '0' }}"
            data-label="{{ $label }}"
            {{ $required ? 'required' : '' }}>{{ $old ?? '' }}</textarea>

    {{-- ── NUMBER ──────────────────────────────────────────────── --}}
    @elseif($type === 'number')
        <input type="number"
            id="{{ $key }}"
            name="{{ $key }}"
            value="{{ $old ?? '' }}"
            class="form-input"
            placeholder="{{ $field->placeholder ?? '0' }}"
            data-required="{{ $required ? '1' : '0' }}"
            data-label="{{ $label }}"
            {{ $field->min !== null ? "min={$field->min}" : '' }}
            {{ $field->max !== null ? "max={$field->max}" : '' }}
            {{ $field->step ? "step={$field->step}" : '' }}
            {{ $required ? 'required' : '' }}>

    {{-- ── DATE ───────────────────────────────────────────────── --}}
    @elseif($type === 'date')
        <input type="date" id="{{ $key }}" name="{{ $key }}" value="{{ $old ?? '' }}" class="form-input" style="cursor:pointer;"
            data-required="{{ $required ? '1' : '0' }}"
            data-label="{{ $label }}"
            {{ $required ? 'required' : '' }}>
        <script>
            (function () {
                const input = document.getElementById('{{ $key }}');
                // Show Indonesian-formatted date as placeholder text via CSS
                function updateDisplay() {
                    if (input.value) {
                        const d = new Date(input.value + 'T00:00:00');
                        input.setAttribute('data-display',
                            d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
                        );
                    } else {
                        input.setAttribute('data-display', '');
                    }
                }
                input.addEventListener('change', updateDisplay);
                updateDisplay();
            })();
        </script>

            {{-- ── SELECT ──────────────────────────────────────────────── --}}
            @elseif($type === 'select')
            <select id="{{ $key }}" name="{{ $key }}" class="form-input"
                style="cursor:pointer;appearance:none;background-image:url(...);"
                data-required="{{ $required ? '1' : '0' }}"
                data-label="{{ $label }}"
                {{ $required ? 'required' : '' }}>
            <option value="">— Pilih —</option>
            @foreach($field->field_options ?? [] as $opt)
                @php $val = is_array($opt) ? ($opt['value'] ?? $opt['label'] ?? $opt) : $opt;
        $lbl = is_array($opt) ? ($opt['label'] ?? $opt['value'] ?? $opt) : $opt; @endphp
                    <option value="{{ $val }}" {{ $old === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>

        {{-- ── CHECKBOX ────────────────────────────────────────────── --}}
    @elseif($type === 'checkbox')
        <label style="display:flex;align-items:center;gap:0.6rem;cursor:pointer;padding:0.5rem 0;">
            <input type="checkbox"
                   id="{{ $key }}"
                   name="{{ $key }}"
                   value="1"
                   style="width:16px;height:16px;border-radius:4px;accent-color:var(--navy-600);flex-shrink:0;"
                   {{ $old ? 'checked' : '' }}>
            <span style="font-size:0.83rem;color:var(--slate-700);">
                {{ $label }}
                @if($required)<span style="color:#dc2626;"> *</span>@endif
            </span>
        </label>

    {{-- ── HEADING ─────────────────────────────────────────────── --}}
    @elseif($type === 'heading')
        <h3 class="form-section-heading">{{ $label }}</h3>

    {{-- ── LOOP STAFF / LOOP OFFICIAL ─────────────────────────── --}}
    @elseif(in_array($type, ['staff_loop', 'official_loop']))
        @php $loopType = $type === 'staff_loop' ? 'staff' : 'official'; @endphp
            <div class="loop-container" data-loop-type="{{ $loopType }}" data-field-key="{{ $field->field_key }}">

                {{-- Header --}}
                <div class="loop-header">
                    <span style="font-size:0.72rem;font-weight:700;color:rgba(255,255,255,0.85);letter-spacing:0.04em;display:flex;align-items:center;gap:0.45rem;">
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="{{ $loopType === 'staff' ? 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0' : 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' }}"/>
                        </svg>
                        @if ($icon)
                            <i class="{{ $icon }} text-gray-400 text-sm flex-shrink-0"></i>
                        @endif
                        {{ $label }}
                        @if($required)<span style="color:rgba(239,68,68,0.7);"> *</span>@endif
                    </span>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <span class="badge badge-gold loop-count hidden" style="font-size:0.62rem;"></span>
                        <div style="display:flex;gap:0.3rem;">
                            <button type="button"
                                onclick="loopSelectAll(this)"
                                style="font-size:0.62rem;padding:0.15rem 0.5rem;border-radius:4px;border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.6);cursor:pointer;font-family:var(--font-body);transition:all 0.15s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.16)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.08)'">Semua</button>
                            <button type="button"
                                onclick="loopDeselectAll(this)"
                                style="font-size:0.62rem;padding:0.15rem 0.5rem;border-radius:4px;border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.6);cursor:pointer;font-family:var(--font-body);transition:all 0.15s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.16)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.08)'">Kosong</button>
                            <button type="button"
                                onclick="loopInvert(this)"
                                style="font-size:0.62rem;padding:0.15rem 0.5rem;border-radius:4px;border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.6);cursor:pointer;font-family:var(--font-body);transition:all 0.15s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.16)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.08)'">Balik</button>
                        </div> 
                        <span style="font-size:0.65rem;color:rgba(255,255,255,0.35);">Drag ⠿ untuk urutkan</span>
                    </div>
                </div>

                {{-- Search --}}
                <div class="loop-search-wrap">
                    <input type="text" class="loop-search" placeholder="Cari nama...">
                </div>

                {{-- List --}}
                <div class="loop-checklist">
                    {{-- Populated by JS (populateLoopLists) --}}
                    <div style="padding:1.5rem;text-align:center;">
                        <div class="sipadu-spinner mx-auto mb-2" style="width:28px;height:28px;border-width:2px;"></div>
                        <p style="font-size:0.75rem;color:var(--slate-300);">Memuat data...</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="loop-footer">
                    @if($field->helper_text)
                        <p style="font-size:0.7rem;color:var(--slate-400);">{{ $field->helper_text }}</p>
                    @else
                        <p style="font-size:0.7rem;color:var(--slate-400);">Centang nama, gunakan ⠿ untuk mengubah urutan tampil di dokumen.</p>
                    @endif
                </div>
            </div>

        {{-- ── REPEATING GROUP ─────────────────────────────────────── --}}
    @elseif($type === 'repeating_group')
        @php
    $groupKey = $field->field_key;
    $childFields = $fields->where('is_group_child', true)
        ->where('group_key', $groupKey);
    $maxRows = $field->max_rows ?? 50;
        @endphp
            <div style="border:1.5px solid var(--slate-200);border-radius:10px;overflow:hidden;background:rgba(255,255,255,0.5);">

                {{-- Group header --}}
                <div style="background:linear-gradient(90deg,var(--slate-100),#f8fafc);padding:0.55rem 0.85rem;border-bottom:1px solid var(--slate-200);display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:0.75rem;font-weight:700;color:var(--slate-700);display:flex;align-items:center;gap:0.4rem;">
                        <svg style="width:12px;height:12px;color:var(--navy-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        {{ $label }}
                        @if($required)<span style="color:#dc2626;"> *</span>@endif
                    </span>
                    @if($field->helper_text)
                        <span style="font-size:0.7rem;color:var(--slate-400);">{{ $field->helper_text }}</span>
                    @endif
                </div>

                {{-- Rows container --}}
                <div id="rows-{{ $docType->key }}-{{ $groupKey }}" style="padding:0.5rem;">
                    {{-- First row rendered server-side --}}
                    <div class="row-item">
                        <div class="grid gap-3" style="grid-template-columns:repeat({{ $childFields->count() }}, 1fr);">
                            @foreach($childFields as $child)
                            <div>
                                <label class="form-label" style="font-size:0.7rem;">
                                    {{ $child->label }}{{ $child->is_required ? ' *' : '' }}
                                </label>
                                @if($child->field_type === 'select')
                                    <select name="field_{{ $child->field_key }}[0][]" class="form-input" style="font-size:0.8rem;">
                                        <option value="">— Pilih —</option>
                                        @foreach($child->options ?? [] as $opt)
                                            @php $v = is_array($opt) ? ($opt['value'] ?? $opt) : $opt;
                $l = is_array($opt) ? ($opt['label'] ?? $opt) : $opt; @endphp
                                            <option value="{{ $v }}">{{ $l }}</option>
                                        @endforeach
                                    </select>
                                @elseif($child->field_type === 'textarea')
                                    <textarea name="field_{{ $child->field_key }}[0][]" class="form-input" rows="2" style="font-size:0.8rem;resize:vertical;" placeholder="{{ $child->placeholder ?? '' }}"></textarea>
                                @else
                                    <input type="{{ $child->field_type === 'number' ? 'number' : ($child->field_type === 'date' ? 'date' : 'text') }}"
                                           name="field_{{ $child->field_key }}[0][]"
                                           class="form-input"
                                           style="font-size:0.8rem;"
                                           placeholder="{{ $child->placeholder ?? '' }}">
                                @endif
                            </div>
                            @endforeach

                            {{-- Remove button placeholder (first row can't be removed if required) --}}
                            <div style="display:flex;align-items:flex-end;padding-bottom:0.1rem;">
                                @if(!$required)
                                <button type="button" onclick="removeRow(this)"
                                    style="padding:0.4rem;border-radius:6px;border:1px solid rgba(239,68,68,0.2);background:rgba(239,68,68,0.05);color:rgba(239,68,68,0.6);cursor:pointer;font-size:0.75rem;transition:all 0.15s;"
                                    onmouseover="this.style.background='rgba(239,68,68,0.12)';this.style.color='rgb(239,68,68)'"
                                    onmouseout="this.style.background='rgba(239,68,68,0.05)';this.style.color='rgba(239,68,68,0.6)'">✕</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Row template for JS cloning --}}
                <template id="row-template-{{ $docType->key }}-{{ $groupKey }}">
                    <div class="row-item">
                        <div class="grid gap-3" style="grid-template-columns:repeat({{ $childFields->count() }}, 1fr);">
                            @foreach($childFields as $child)
                            <div>
                                @if($child->field_type === 'select')
                                    <select name="field_{{ $child->field_key }}[__INDEX__][]" class="form-input" style="font-size:0.8rem;">
                                        <option value="">— Pilih —</option>
                                        @foreach($child->options ?? [] as $opt)
                                            @php $v = is_array($opt) ? ($opt['value'] ?? $opt) : $opt;
                $l = is_array($opt) ? ($opt['label'] ?? $opt) : $opt; @endphp
                                            <option value="{{ $v }}">{{ $l }}</option>
                                        @endforeach
                                    </select>
                                @elseif($child->field_type === 'textarea')
                                    <textarea name="field_{{ $child->field_key }}[__INDEX__][]" class="form-input" rows="2" style="font-size:0.8rem;resize:vertical;" placeholder="{{ $child->placeholder ?? '' }}"></textarea>
                                @else
                                    <input type="{{ $child->field_type === 'number' ? 'number' : ($child->field_type === 'date' ? 'date' : 'text') }}"
                                           name="field_{{ $child->field_key }}[__INDEX__][]"
                                           class="form-input"
                                           style="font-size:0.8rem;"
                                           placeholder="{{ $child->placeholder ?? '' }}">
                                @endif
                            </div>
                            @endforeach
                            <div style="display:flex;align-items:flex-end;padding-bottom:0.1rem;">
                                <button type="button" onclick="removeRow(this)"
                                    style="padding:0.4rem;border-radius:6px;border:1px solid rgba(239,68,68,0.2);background:rgba(239,68,68,0.05);color:rgba(239,68,68,0.6);cursor:pointer;font-size:0.75rem;transition:all 0.15s;"
                                    onmouseover="this.style.background='rgba(239,68,68,0.12)';this.style.color='rgb(239,68,68)'"
                                    onmouseout="this.style.background='rgba(239,68,68,0.05)';this.style.color='rgba(239,68,68,0.6)'">✕</button>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Footer: Add row --}}
                <div style="padding:0.55rem 0.85rem;border-top:1px solid var(--slate-100);display:flex;align-items:center;justify-content:space-between;">
                    <button type="button"
                        onclick="addRow('{{ $docType->key }}', '{{ $groupKey }}')"
                        style="display:flex;align-items:center;gap:0.4rem;padding:0.35rem 0.8rem;border-radius:7px;border:1.5px dashed var(--navy-200);background:transparent;color:var(--navy-600);font-size:0.75rem;font-weight:600;cursor:pointer;transition:all 0.2s;font-family:var(--font-body);"
                        onmouseover="this.style.borderColor='var(--navy-400)';this.style.background='rgba(42,82,152,0.05)'"
                        onmouseout="this.style.borderColor='var(--navy-200)';this.style.background='transparent'">
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        + Tambah Baris
                    </button>
                    <span style="font-size:0.68rem;color:var(--slate-300);">maks. {{ $maxRows }} baris</span>
                </div>
            </div>

        {{-- ── FALLBACK ─────────────────────────────────────────────── --}}
    @else
        <input type="text"
               id="{{ $key }}"
               name="{{ $key }}"
               value="{{ $old ?? '' }}"
               class="form-input"
               placeholder="{{ $field->placeholder ?? '' }}"
               {{ $required ? 'required' : '' }}>
    @endif

    {{-- Validation error --}}
    @error($key)
        <p style="font-size:0.73rem;color:#dc2626;margin-top:0.3rem;display:flex;align-items:center;gap:0.35rem;">
            <svg style="width:12px;height:12px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $message }}
        </p>
    @enderror

</div>
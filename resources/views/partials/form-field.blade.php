{{--
    Partial: partials/form-field.blade.php
    Variables: $field, $docType, $fields
--}}
@php
    $key      = "field_{$field->field_key}";
    $label    = $field->label;
    $required = $field->is_required;
    $type     = $field->field_type;
    $old      = old($key);
@endphp

<div data-field-key="{{ $field->field_key }}" class="field-wrapper">

    {{-- ── Label ─────────────────────────────────────────────── --}}
    @if(!in_array($type, ['loop_staff','loop_official','repeating_group','checkbox','heading']))
    <label class="form-label" for="{{ $key }}">
        {{ $label }}
        @if($required)
            <span style="color:#dc2626;margin-left:0.15rem;">*</span>
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
        <input type="text"
               id="{{ $key }}"
               name="{{ $key }}"
               value="{{ $old ?? '' }}"
               class="form-input"
               placeholder="{{ $field->placeholder ?? '' }}"
               {{ $required ? 'required' : '' }}
               {{ $field->maxlength ? "maxlength={$field->maxlength}" : '' }}>

    {{-- ── TEXTAREA ────────────────────────────────────────────── --}}
    @elseif($type === 'textarea')
        <textarea id="{{ $key }}"
                  name="{{ $key }}"
                  class="form-input"
                  rows="{{ $field->rows ?? 3 }}"
                  style="resize:vertical;"
                  placeholder="{{ $field->placeholder ?? '' }}"
                  {{ $required ? 'required' : '' }}>{{ $old ?? '' }}</textarea>

    {{-- ── NUMBER ──────────────────────────────────────────────── --}}
    @elseif($type === 'number')
        <input type="number"
               id="{{ $key }}"
               name="{{ $key }}"
               value="{{ $old ?? '' }}"
               class="form-input"
               placeholder="{{ $field->placeholder ?? '0' }}"
               {{ $field->min !== null ? "min={$field->min}" : '' }}
               {{ $field->max !== null ? "max={$field->max}" : '' }}
               {{ $field->step       ? "step={$field->step}" : '' }}
               {{ $required ? 'required' : '' }}>

    {{-- ── DATE ───────────────────────────────────────────────── --}}
    @elseif($type === 'date')
        {{-- Hidden date input (raw value) --}}
        <input type="date"
               id="{{ $key }}_raw"
               name="{{ $key }}"
               value="{{ $old ?? '' }}"
               class="hidden"
               {{ $required ? 'required' : '' }}>
        {{-- Visual display input (Indonesian locale) --}}
        <input type="text"
               id="{{ $key }}_display"
               class="form-input"
               placeholder="dd / mm / yyyy"
               readonly
               style="cursor:pointer;"
               onclick="document.getElementById('{{ $key }}_raw').showPicker?.()">
        {{-- Format bridge --}}
        <script>
        (function() {
            const raw     = document.getElementById('{{ $key }}_raw');
            const display = document.getElementById('{{ $key }}_display');
            function formatId(val) {
                if (!val) return '';
                const d = new Date(val + 'T00:00:00');
                return d.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
            }
            raw.addEventListener('change', function() {
                display.value = formatId(this.value);
            });
            if (raw.value) display.value = formatId(raw.value);
        })();
        </script>

    {{-- ── SELECT ──────────────────────────────────────────────── --}}
    @elseif($type === 'select')
        <select id="{{ $key }}"
                name="{{ $key }}"
                class="form-input"
                style="cursor:pointer;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:1rem;padding-right:2.25rem;"
                {{ $required ? 'required' : '' }}>
            <option value="">— Pilih —</option>
            @foreach($field->options ?? [] as $opt)
                @php $val = is_array($opt) ? ($opt['value'] ?? $opt['label'] ?? $opt) : $opt; $lbl = is_array($opt) ? ($opt['label'] ?? $opt['value'] ?? $opt) : $opt; @endphp
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
    @elseif(in_array($type, ['loop_staff', 'loop_official']))
        @php $loopType = $type === 'loop_staff' ? 'staff' : 'official'; @endphp
        <div class="loop-container" data-loop-type="{{ $loopType }}" data-field-key="{{ $field->field_key }}">

            {{-- Header --}}
            <div class="loop-header">
                <span style="font-size:0.72rem;font-weight:700;color:rgba(255,255,255,0.85);letter-spacing:0.04em;display:flex;align-items:center;gap:0.45rem;">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="{{ $loopType === 'staff' ? 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0' : 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' }}"/>
                    </svg>
                    {{ $label }}
                    @if($required)<span style="color:rgba(239,68,68,0.7);"> *</span>@endif
                </span>
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <span class="badge badge-gold loop-count hidden" style="font-size:0.62rem;"></span>
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
            $groupKey     = $field->field_key;
            $childFields  = $fields->where('is_group_child', true)
                                   ->where('group_parent_key', $groupKey);
            $maxRows      = $field->max_rows ?? 50;
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
                                        @php $v = is_array($opt) ? ($opt['value'] ?? $opt) : $opt; $l = is_array($opt) ? ($opt['label'] ?? $opt) : $opt; @endphp
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
                                        @php $v = is_array($opt) ? ($opt['value'] ?? $opt) : $opt; $l = is_array($opt) ? ($opt['label'] ?? $opt) : $opt; @endphp
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
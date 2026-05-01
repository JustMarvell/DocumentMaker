<tr data-id="{{ $field->id }}" class="hover:bg-gray-50" draggable="true">
    <td class="px-2 py-3 text-gray-300 cursor-grab select-none text-base">⠿</td>
    <td class="px-3 py-3 text-center">
        @if ($field->icon)
            <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-blue-50 text-blue-500 text-sm" title="{{ $field->icon }}">
                <i class="{{ $field->icon }}"></i>
            </span>
        @else
            <span class="text-gray-300 text-xs">—</span>
        @endif
    </td>
    <td class="px-3 py-3">
        <span class="font-mono text-xs text-gray-400 block">{{ $field->field_key }}</span>
        <span class="text-gray-700 text-xs block">{{ $field->label }}</span>
        @if ($field->section_label)
            <span class="text-blue-400 text-xs block">§ {{ $field->section_label }}</span>
        @endif
        @if ($field->is_group_child)
            <span class="text-purple-400 text-xs block">↳ {{ $field->group_key }}</span>
        @endif
    </td>
    <td class="px-3 py-3">
        <span class="px-1.5 py-0.5 rounded text-xs font-medium
            {{ in_array($field->field_type, ['staff_loop', 'official_loop']) ? 'bg-green-100 text-green-700' :
    ($field->field_type === 'repeating_group' ? 'bg-purple-100 text-purple-700' :
        ($field->field_type === 'select' ? 'bg-yellow-100 text-yellow-700' :
            'bg-gray-100 text-gray-600')) }}">
            {{ $field->field_type }}
        </span>
    </td>
    <td class="px-3 py-3 text-xs">
        @if ($field->staff_autofill_column && $field->autofill_role !== 'none')
            <span class="block text-gray-500">{{ $field->staff_autofill_column }}</span>
            <code class="px-1.5 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-600">
                {{ $field->autofill_role }}
            </code>
        @else
            <span class="text-gray-300">—</span>
        @endif
    </td>
    <td class="px-3 py-3 text-center text-xs text-gray-400">
        {{ $field->row_group ?? '—' }}
    </td>
    <td class="px-3 py-3 text-center">
        @if ($field->is_required)
            <span class="text-green-600 font-bold">✓</span>
        @else
            <span class="text-gray-300">—</span>
        @endif
    </td>
    <td class="px-3 py-3">
        <div class="flex flex-col gap-1">
            <button type="button"
                onclick="openEditField({{ $field->id }})"
                class="text-xs px-2 py-1 rounded border border-blue-400 text-blue-600 hover:bg-blue-50 text-center">
                Edit
            </button>
            <button type="button"
                onclick="deleteField({{ $field->id }}, '{{ addslashes($field->label) }}')"
                class="text-xs px-2 py-1 rounded border border-red-400 text-red-500 hover:bg-red-50 text-center">
                Hapus
            </button>
        </div>
    </td>
</tr>
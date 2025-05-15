<tr data-repeater-item>
    <td style="display:flex;justify-content: center;">
        <i class="bx bx-menu bx-md drag-handle" aria-hidden="true"></i>
    </td>
    <td>
        <input type="text" id="form-repeater-{{ $i }}-1" data-name="name" name="comparison[{{ $i }}][name]" value="{{ $name }}" class="form-control" required>
    </td>
    <td>
        <input type="text" id="form-repeater-{{ $i }}-2" data-name="description" name="comparison[{{ $i }}][description]" value="{{ $description }}" class="form-control">
    </td>
    <td>
        <select id="form-repeater-{{ $i }}-3" data-name="type" name="comparison[{{ $i }}][type]" class="form-select" title="Select a value type">
            <option {{ $type == 0 ? 'selected' : '' }} value="0">{{ __('Arrow Checks') }} ✔️ | ❌</option>
            <option {{ $type == 1 ? 'selected' : '' }} value="1">{{ __('Custom Text (Supports HTML)') }}</option>
        </select>
    </td>
    <td>
        <button type="button" class="btn btn-label-danger" data-repeater-delete="">
            <i class="bx bx-x"></i>
        </button>
    </td>
    <input type="hidden" id="form-repeater-{{ $i }}-4" data-name="id" name="comparison[{{ $i }}][id]" value="{{ $id }}" class="form-control">
</tr>

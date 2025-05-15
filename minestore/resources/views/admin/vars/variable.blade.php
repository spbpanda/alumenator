<div data-repeater-item>
  <div class="row">
      <div class="col-sm-1 mb-0 text-center" style="align-self: center;">
          <i class="bx bx-menu bx-md drag-handle" aria-hidden="true"></i>
      </div>
      <div class="mb-3 col-sm-3">
        <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-1">
            {{ __('Name') }}
            <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="{{ __('The option will be displayed in dropdown." data-bs-original-title="The option will be displayed in dropdown.') }}"></i>
        </label>
        <input type="text" id="form-repeater-{{ $i }}-1" data-name="name" name="variables[{{ $i }}][name]" value="{{ $name }}" class="form-control" placeholder="Red Color">
      </div>
      <div class="mb-3 col-sm-4">
        <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-2">
            {{ __('Option Value') }}
          <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="{{ __('The value that will be used while command getting executed." data-bs-original-title="The value that will be used while command getting executed.') }}"></i>
        </label>
        <input type="text" id="form-repeater-{{ $i }}-2" data-name="value" name="variables[{{ $i }}][value]" value="{{ $value }}" class="form-control" placeholder="red">
      </div>
      <div class="mb-3 col-sm-3">
        <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-3">
            {{ __('Additional Fee') }}
          <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="{{ __('Additional fee that will be added to the package price." data-bs-original-title="Additional fee that will be added to the package price.') }}"></i>
        </label>
        <div class="input-group mb-2">
          <input type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$" id="form-repeater-{{ $i }}-3" data-name="price" name="variables[{{ $i }}][price]" value="{{ $price }}" class="form-control">
          <span class="input-group-text">{{ $settings->currency }}</span>
        </div>
      </div>
      <div class="mb-3 col-sm-1" style="align-self: center;">
          <button type="button" class="btn btn-label-danger mt-3" data-repeater-delete="">
              <i class="bx bx-x"></i>
          </button>
      </div>
  </div>
  <hr class="mt-2">
</div>

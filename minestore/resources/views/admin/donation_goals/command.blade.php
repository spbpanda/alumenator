<div data-repeater-item>
    <div class="row mt-2">
        <div class="col-sm-11 mb-3">
            <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-1">{{ __('Command to Execute') }}</label>
            <input type="text" id="form-repeater-{{ $i }}-1" name="commands[{{ $i }}][cmd]" value="{{ $command }}" class="form-control" placeholder="{{  __('Enter the command to execute on your server. Use {} to use variables.') }}">
        </div>
        <div class="col-sm-1 d-flex align-items-center mb-3">
            <button type="button" class="btn btn-label-danger mt-4" data-repeater-delete="">
                <i class="bx bx-x"></i>
            </button>
        </div>
    </div>
</div>

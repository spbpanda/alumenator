<div class="col-md-12 giftcardBlock">
<!-- @if(!$isItemExist || $item->type != 1) style="display: none;" @endif -->
    <div class="card mb-3">
        <div class="card-header border-bottom mb-3">
            <h5 class="card-title">{{ __('Selling Gift Card') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 mb-2">
                    <label for="giftcard_price" class="form-label">
                        {{ __('Create a Gift Card') }}
                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('You can set the amount of money supplied in this gift card.') }}"></i>
                    </label>
                    <div class="input-group">
                      <input id="giftcard_price" name="giftcard_price" type="text" placeholder="{{ __('Enter an amount to send gift card to the customer after purchase.') }}" class="form-control" value="{{ $isItemExist && $item->type == 1 ? $item->giftcard_price : "" }}">
                      <span class="input-group-text">{{ $settings->currency }}</span>
                      <span class="input-group-text">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_giftcard_same_price" name="is_giftcard_same_price">
                            <label class="form-check-label" for="is_giftcard_same_price">
                                {{ __('Same as Package Price') }}
                            </label>
                        </div>
                      </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

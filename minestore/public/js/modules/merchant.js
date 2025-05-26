/**
 * Enable/disable merchant
 * @param type - merchant name
 * @param value - true or false
 */
function updateMerchantState(type, value) {
    $.ajax({
        type: "PATCH",
        url: `/api/admin/settings/merchant/${type}`,
        data: { enable: value },
        success: function() {
            toastr.success("Changes successfully saved!");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            const status = parseInt(jqXHR.status) || 0;
            const errorMessage = jqXHR.responseJSON?.message || "{{ __('Unable to save changes!') }}";
            if (status === 422) {
                toastr.error(errorMessage);
            } else if (status === 403) {
                toastr.error(errorMessage);
            } else if (status === 404) {
                toastr.warning(errorMessage);
            } else {
                toastr.error(errorMessage);
            }
        }
    });
}

/**
 * Update merchant configuration
 * @param type - merchant name
 * @param data - array of attributes
 */
function updateMerchantConfig(type, data) {
    return $.ajax({
        type: "PATCH",
        url: `/api/admin/settings/merchant/${type}`,
        data: data
    });
}

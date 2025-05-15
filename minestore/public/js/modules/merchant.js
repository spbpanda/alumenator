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
        error: function() {
            toastr.error("Unable to save changes!");
        }
    });
}

/**
 * Enable/disable PayPal merchant
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

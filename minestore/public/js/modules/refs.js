/**
 * Delete ref code
 * @param id - code id
 */
function deleteCode(id) {
    return $.ajax({
        type: "DELETE",
        url: `/api/admin/refs/${id}`
    });
}

function updateIsRefEnabledSetting(value) {
    $.ajax({
        type: "PATCH",
        url: "/api/admin/refs/enabledUpdate",
        data: { isRefEnabled: value },
        success: function() {
            toastr.success("Changes are Successfully Saved!");
        },
        error: function() {
            toastr.error("Unable to Save Changes!");
        }
    });
}
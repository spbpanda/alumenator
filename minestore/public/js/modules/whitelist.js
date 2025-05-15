/**
 * Add user to the whitelist
 * @param username - user's nickname
 */
function addUserToWhitelist(username) {
    return $.ajax({
        type: "POST",
        url: "/api/admin/whitelist",
        data: { username: username }
    });
}

/**
 * Remove user from the whitelist
 * @param id - user id
 */
function removeUserFromWhitelist(id) {
    $.ajax({
        type: "DELETE",
        url: `/api/admin/whitelist/${id}`,
        success: function(r) {
            toastr.success("User was removed!");
        },
        error: function(r) {
            toastr.error("Unable to remove user!");
        }
    });
}

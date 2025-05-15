/**
 * Delete a user
 * @param id - user id
 */
function deleteUser(id) {
    return $.ajax({
        type: "DELETE",
        url: `/api/admin/users/${id}`
    });
}

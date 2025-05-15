/**
 * Read notification
 * @param id - notification id
 */
function readNotification(id) {
    return $.ajax({
        type: "POST",
        url: `/api/admin/notifications/${id}/read`
    });
}

/**
 * Read all notifications
 */
function readAllNotifications() {
    return $.ajax({
        type: "POST",
        url: `/api/admin/notifications/read-all`
    });
}

/**
 * Delete notification
 * @param id - notification id
 */
function deleteNotification(id) {
    return $.ajax({
        type: "DELETE",
        url: `/api/admin/notifications/${id}`
    });
}

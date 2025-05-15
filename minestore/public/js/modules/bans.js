/**
 * Ban user
 * @param username - user's nickname
 */
function banUser(username) {
    return $.ajax({
        type: "POST",
        url: "/api/admin/banlist",
        data: { username: username }
    });
}

/**
 * Unban user
 * @param id - user id
 */
function unbanUser(id) {
    return $.ajax({
        type: "DELETE",
        url: `/api/admin/banlist/${id}`
    });
}


// Control button style
function switchMainBanButton($btn) {
    if ($btn.hasClass("btn-danger")) {
        $btn.removeClass("btn-danger");
        $btn.addClass("btn-success");
        $btn.html(`<span class="tf-icons bx bx-check-square me-1"></span>Unban User`);
    } else {
        $btn.removeClass("btn-success");
        $btn.addClass("btn-danger");
        $btn.html(`<span class="tf-icons bx bx-message-square-x me-1"></span>Ban User`);
    }
}

function switchSecondaryBanButton($btn) {
    if ($btn.hasClass("btn-danger")) {
        $btn.removeClass("btn-danger");
        $btn.addClass("btn-success");
        $btn.html(`<span class="tf-icons bx bx-repeat me-1"></span>Unban User`);
    } else {
        $btn.removeClass("btn-success");
        $btn.addClass("btn-danger");
        $btn.html(`<span class="tf-icons bx bxs-trash-alt me-1"></span>Ban User`);
    }
}

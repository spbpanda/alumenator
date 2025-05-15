/**
 * Mark chargeback as done
 * @param id - chargeback id
 */
function finishChargeback(id) {
    return $.ajax({
        type: "PATCH",
        url: `/api/admin/chargeback/${id}/finish`,
    });
}

/**
 * Delete chargeback
 * @param id - chargeback id
 */
function deleteChargeback(id) {
    return $.ajax({
        type: "DELETE",
        url: `/api/admin/chargeback/${id}`
    });
}

/**
 * Submit chargeback to Stripe
 * @param id - chargeback id
 */
function submitChargeback(id) {
    return $.ajax({
        type: "POST",
        url: `/api/admin/chargeback/${id}/submit`
    });
}

/**
 * Resend command
 * @param commandHistoryId - commandHistory Id
 */
function reSendCommand(commandHistoryId) {
    return $.ajax({
        type: "POST",
        url: `/admin/payments/resend/${commandHistoryId}`,
    });
}

/**
 * Resend all commands
 * @param paymentId - payment id
 */
function resendAllCommands(paymentId) {
    return $.ajax({
        type: "POST",
        url: `/admin/payments/resend/all/${paymentId}`,
    });
}

/**
 * Delete command
 * @param commandHistoryId - commandHistory Id
 */

function deleteCommand(commandHistoryId) {
    return $.ajax({
        type: "POST",
        url: `/admin/payments/delete/cmd/${commandHistoryId}`,
    });
}

/**
 * Delivery items
 * @param paymentId - payment id
 */
function deliveryItems(paymentId) {
    return $.ajax({
        type: "POST",
        url: `/api/admin/payments/${paymentId}/delivery`,
    });
}

/**
 * Mark payment as paid
 * @param id - payment id
 */
function markPaid(id) {
    return $.ajax({
        type: "POST",
        url: `/admin/payments/markAsPaid/${id}`
    });
}

/**
 * Delete payment
 * @param id - payment id
 */
function deletePayment(id) {
    return $.ajax({
        type: "DELETE",
        url: `/api/admin/payments/${id}`
    });
}

/**
 * Add note to the payment
 * @param id - payment id
 * @param note - note text
 */
function addPaymentNote(id, note) {
    return $.ajax({
        type: "POST",
        url: `/api/admin/payments/${id}/note`,
        data: {note: note}
    });
}

/**
 * Enable Collecting Details for the payments during checkout
 * @param Request $r
 * @return JsonResponse
 */
function updateIsDetailsEnabledSetting(value) {
    $.ajax({
        type: "PATCH",
        url: "/api/admin/payments/enabledUpdate",
        data: { isDetailsEnabled: value },
        success: function() {
            toastr.success("Changes are Successfully Saved!");
        },
        error: function() {
            toastr.error("Unable to Save Changes!");
        }
    });
}

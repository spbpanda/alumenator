/**
 * Close Subscription
 * @param subscriptionId - payment ID
 */
function closeSubscription(subscriptionId) {
    return $.ajax({
        type: "POST",
        url: `/admin/subscriptions/close/${subscriptionId}`,
    });
}

/**
 * Delete coupon
 * @param id - coupon id
 */
function deleteCoupon(id) {
    return $.ajax({
        type: "DELETE",
        url: `/api/admin/coupons/${id}`
    });
}

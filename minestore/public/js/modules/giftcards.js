/**
 * Delete giftcard
 * @param id - giftcard id
 */
function deleteGiftcard(id) {
    return $.ajax({
        type: "DELETE",
        url: `/api/admin/giftcards/${id}`
    });
}

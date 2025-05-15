// Update is_featured setting
// Promoted packages page
function updateIsFeaturedSetting(value) {
    $.ajax({
        type: "POST",
        url: "/admin/promoted/settings",
        data: { is_featured: value },
        success: function() {
            toastr.success("Changes successfully saved!");
        },
        error: function() {
            toastr.error("Unable to save changes!");
        }
    });
}


// Update is_featured_offer setting
// Promoted packages page
function updateIsFeaturedOfferSetting(value) {
    $.ajax({
        type: "POST",
        url: "/admin/promoted/settings",
        data: { is_featured_offer: value },
        success: function() {
            toastr.success("Changes successfully saved!");
        },
        error: function() {
            toastr.error("Unable to save changes!");
        }
    });
}

/**
 * Update items sorting on items page
 * @param fromCategoryId - old category id
 * @param toCategoryId - new category id
 * @param oldIndex - old position
 * @param newIndex - new position
 * @param objectId - item id
 */
function updateItemSorting(fromCategoryId, toCategoryId, oldIndex, newIndex, objectId) {
    if (fromCategoryId === toCategoryId && oldIndex === newIndex) {
        return;
    }

    $.ajax({
        type: "POST",
        url: "/admin/items/update-sort",
        data: {
            type: "item",
            from_category_id: fromCategoryId,
            to_category_id: toCategoryId,
            old_index: oldIndex,
            new_index: newIndex,
            object_id: objectId
        },
        success: function(r) {
            toastr.success("Changes successfully saved!");
        },
        error: function(r) {
            toastr.error("Unable to save changes!");
        }
    });
}

/**
 * Update categories sorting on items page
 * @param fromCategoryId - old category id
 * @param toCategoryId - new category id
 * @param oldIndex - old position
 * @param newIndex - new position
 * @param objectId - category id
 */
function updateCategorySorting(fromCategoryId, toCategoryId, oldIndex, newIndex, objectId) {
    if (fromCategoryId === toCategoryId && oldIndex === newIndex) {
        return;
    }

    $.ajax({
        type: "POST",
        url: "/admin/items/update-sort",
        data: {
            type: "category",
            from_category_id: fromCategoryId,
            to_category_id: toCategoryId,
            old_index: oldIndex,
            new_index: newIndex,
            object_id: objectId
        },
        success: function(r) {
            toastr.success("Changes successfully saved!");
        },
        error: function(r) {
            toastr.error("Unable to save changes!");
        }
    });
}

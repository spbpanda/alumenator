/**
 * Enable/disable staff module
 * @param value
 */
function updateIsStaffPageEnabledSetting(value) {
    $.ajax({
        type: "PATCH",
        url: "/api/admin/pages/staff",
        data: { is_staff_page_enabled: value },
        success: function() {
            toastr.success("Changes successfully saved!");
        },
        error: function() {
            toastr.error("Unable to save changes!");
        }
    });
}

/**
 * Update is prefix first
 * @param value
 */
function updateIsPrefixEnabledSetting(value) {
    $.ajax({
        type: "PATCH",
        url: "/api/admin/pages/staff",
        data: { is_prefix_enabled: value },
        success: function() {
            toastr.success("Changes successfully saved!");
        },
        error: function() {
            toastr.error("Unable to save changes!");
        }
    });
}

/**
 * Update enabled ranks
 * @param value
 */
function updateEnabledRanksSetting(value) {
    $.ajax({
        type: "PATCH",
        url: "/api/admin/pages/staff",
        data: { enabled_ranks: value },
        success: function() {
            toastr.success("Changes successfully saved!");
        },
        error: function() {
            toastr.error("Unable to save changes!");
        }
    });
}

/**
 * Update items sorting on staff page
 * @param group - group name
 * @param oldIndex - old position
 * @param newIndex - new position
 */
function updateStaffItemSorting(group, oldIndex, newIndex) {
    if (oldIndex === newIndex) {
        return;
    }

    $.ajax({
        type: "PATCH",
        url: "/api/admin/pages/staff/update-sort",
        data: {
            type: "item",
            player_group: group,
            old_index: oldIndex,
            new_index: newIndex
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
 * Update groups sorting on staff page
 * @param oldIndex - old position
 * @param newIndex - new position
 */
function updateStaffGroupSorting(oldIndex, newIndex) {
    if (oldIndex === newIndex) {
        return;
    }

    $.ajax({
        type: "PATCH",
        url: "/api/admin/pages/staff/update-sort",
        data: {
            type: "group",
            old_index: oldIndex,
            new_index: newIndex
        },
        success: function(r) {
            toastr.success("Changes successfully saved!");
        },
        error: function(r) {
            toastr.error("Unable to save changes!");
        }
    });
}

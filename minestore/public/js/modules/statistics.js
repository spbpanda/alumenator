/**
 * Get income by players data
 */
function getIncomeByPlayers(filter) {
    return $.ajax({
        type: "GET",
        url: `/api/admin/statistics/income-by-players?filter=${filter}`
    });
}

/**
 * Get total revenue data
 */
function getTotalRevenueData(year) {
    return $.ajax({
        type: "GET",
        url: `/api/admin/statistics/total-revenue?year=${year}`
    });
}

function buildPlayerItem(player){
    return `<li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3">
                    <img src="https://mc-heads.net/avatar/${player.username}/30" alt="User" class="rounded">
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                        <h6 class="mb-0">${player.username}</h6>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-1">
                        <h6 class="mb-0">${player.total_value}</h6> <span
                            class="text-muted">${player.currency}</span>
                        <h6 class="mb-0"><span class="text-muted">(</span>${player.total_records}
                            <span class="text-muted"> Transactions)</span></h6>
                    </div>
                </div>
            </li>`
}

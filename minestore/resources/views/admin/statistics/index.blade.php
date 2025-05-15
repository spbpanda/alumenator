@extends('admin.layout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/apex-charts/apex-charts.css')}}">
    <link rel="stylesheet" href="{{asset('res/vendor/libs/flatpickr/flatpickr.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/pickr/pickr-themes.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/apex-charts/apexcharts.js')}}"></script>
    <script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('res/js/cards-statistics.js')}}"></script>
    <script src="{{asset('res/js/ui-cards-analytics.js')}}"></script>
    <script src="{{asset('js/modules/statistics.js')}}"></script>
    <script>
        buildActivityChart({!! json_encode($global->chartData->categories) !!}, {!! json_encode($global->chartData->data) !!});
        @if(!empty($weeklyVisits))buildWeeklyVisitsChart({!! json_encode($weeklyVisits->chartData->categories) !!}, {!! json_encode($weeklyVisits->chartData->data) !!}, '{{ now()->shortDayName }}');@endif
        const totalRevenueChart = buildTotalRevenueChart({!! json_encode($total->currentYear) !!}, {!! json_encode($total->previousYear) !!});
        const growthChart = buildGrowthChart({{$total->difference}});
        buildPopularPackagesChart({!! json_encode($topPackages->categories) !!}, {!! json_encode($topPackages->data) !!})

        const playerList = $("#players");
        const currency = '{{ $settings->currency }}';
        $("#last-days").click(function() {
            getIncomeByPlayers("28-days").done(function(r) {
                playerList.html("");
                Object.values(r).forEach(function(item) {
                    playerList.append(buildPlayerItem(item));
                });
            }).fail(function() {
                toastr.error("Unable to update data!");
            });
        });

        $("#last-month").click(function() {
            getIncomeByPlayers("month").done(function(r) {
                playerList.html("");
                Object.values(r).forEach(function(item) {
                    playerList.append(buildPlayerItem(item));
                });
            }).fail(function(r) {
                toastr.error("Unable to update data!");
            });
        });

        $("#last-year").click(function() {
            getIncomeByPlayers("year").done(function(r) {
                playerList.html("");
                Object.values(r).forEach(function(item) {
                    playerList.append(buildPlayerItem(item));
                });
            }).fail(function() {
                toastr.error("Unable to update data!");
            });
        });

        $(".choose-year").click(function(){
            const year = $(this).attr('data-year');
            $("#growthReportId").text(year);
            getTotalRevenueData(year).done(function(r) {
                totalRevenueChart.updateSeries([{
                    name: r.currentYear.name,
                    data: r.currentYear.data
                },
                    {
                        name: r.previousYear.name,
                        data: r.previousYear.data
                    }
                ]);

                growthChart.updateSeries([r.difference]);

                $("#yearDifference").text(r.difference + '% Revenue Growth');
                $("#currentYearName").text(r.currentYear.name);
                $("#currentYearSum").text(r.currentYear.formatted_sum + ' ' + currency);
                $("#previousYearName").text(r.previousYear.name);
                $("#previousYearSum").text(r.previousYear.formatted_sum + ' ' + currency);
            }).fail(function(r) {
                toastr.error("Unable to update data!");
            });
        });

        document.querySelector("#range").flatpickr({
            mode: "range",
            enableTime: true,
            defaultDate: ["today", "today"],
            dateFormat: "Y-m-d H:i",
        });

        $('#btnFilterDates').on('click', function (){
            let dates = $('#range').val();
            let from = dates.split(' to ')[0];
            let until = dates.split(' to ')[1];
            window.location.href = `{{ route('statistics.getByFilter') }}?from=${from}&until=${until}`;
        });
    </script>
@endsection

@section('content')
    <div class="row mb-3">
        <div class="col-md-10">
            <label for="range" class="form-label">
                {{ __('Statistics for selected range of dates') }}
                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                   data-bs-toggle="tooltip" data-bs-placement="top"
                   title="{{ __('Select the range between dates to receive statistic for specific days.') }}">
                </i>
            </label>
            <input type="text" class="form-control" id="range" name="range" value="{{ old('range') }}"
                   placeholder="YYYY-MM-DD HH:MM" />
            @error('range')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-2 justify-content-center align-content-center">
            <label for="button" class="form-label">
                &nbsp;
            </label>
            <button type="button" class="btn btn btn-primary w-100" id="btnFilterDates">{{ __('Update Statistics') }}</button>
        </div>
    </div>
    <div class="row mainStatistics">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">{{ __('Congratulations') }} @if (Auth::guard('admins')->check()) {{ Auth::guard('admins')->user()->username }}@else Unknown User @endif!</h5>
                            <p class="mb-4">{{ __('You have') }} <span class="fw-bold">72% </span>{{ __('more sales than regular
                                MineStoreCMS webstore. Unlock all tools to increase your sales with') }} <span
                                    class="fw-bold">{{ __('Ultimate') }}</span>.</p>

                            <a href="https://minestorecms.com/pricing" class="btn btn-sm btn-label-primary">{{ __('Join Ultimate Team') }}</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{asset('res/img/illustrations/man-with-laptop-dark.png')}}" height="140"
                                 alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                 data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(!empty($weeklyVisits))
            <div class="col-lg-4 col-md-4 order-1 mainStatistics">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body pb-0">
                                <span class="d-block fw-semibold mb-1">{{ __('Weekly Visits') }}</span>
                                <h3 class="card-title mb-1">{{$weeklyVisits->amount}}</h3>
                            </div>
                            <div id="revenueChart"></div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{asset('res/img/icons/unicons/wallet-info.png')}}" alt="Credit Card"
                                             class="rounded">
                                    </div>
                                </div>
                                <span>{{ __('Today Sales') }}</span>
                                <h3 class="card-title text-nowrap mb-1">{{ $today->amount }} {{ $settings->currency }}</h3>
                                <small
                                    class="@if($today->level == 'up') text-success @elseif($today->level == 'down') text-danger @else text-secondary @endif fw-semibold">
                                    @if($today->level == 'up')
                                        <i class='bx bx-up-arrow-alt'></i>
                                    @elseif($today->level == 'down')
                                        <i class='bx bx-down-arrow-alt'></i>
                                    @else
                                        ~
                                    @endif
                                    {{ $today->difference }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Total Revenue -->
        <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-md-8">
                        <h5 class="card-header m-0 me-2 pb-3">{{ __('Total Revenue') }}</h5>
                        <div id="totalRevenueChart" class="px-2"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-label-primary dropdown-toggle" type="button"
                                            id="growthReportId" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        {{ $total->currentYear->name }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                                        @foreach($yearsRevenue as $yearRevenue)
                                            <a class="dropdown-item choose-year" data-year="{{ $yearRevenue->year }}" href="javascript:void(0);">{{ $yearRevenue->year }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="growthChart"></div>
                        <div class="text-center fw-semibold pt-3 mb-2" id="yearDifference">{{$total->difference}}% {{ __('Revenue Growth') }}</div>

                        <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
                            <div class="d-flex">
                                <div class="me-2">
                                    <span class="badge bg-label-primary p-2"><i
                                            class="bx bx-dollar text-primary"></i></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <small id="currentYearName">{{$total->currentYear->name}}</small>
                                    <h6 class="mb-0" id="currentYearSum">{{$total->currentYear->formatted_sum}} {{$settings->currency}}</h6>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="me-2">
                                    <span class="badge bg-label-info p-2"><i class="bx bx-wallet text-info"></i></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <small id="previousYearName">{{$total->previousYear->name}}</small>
                                    <h6 class="mb-0" id="previousYearSum">{{$total->previousYear->formatted_sum}} {{$settings->currency}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Total Revenue -->
        <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
            <div class="row">
                @if(!empty($weekly))
                    <div class="col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{asset('res/img/icons/unicons/rocket.png')}}" alt="rocket"
                                             class="rounded">
                                    </div>
                                </div>
                                <span class="d-block mb-1">{{ __('Weekly Revenue') }}</span>
                                <h3 class="card-title text-nowrap mb-2">{{ $weekly->amount }} {{ $settings->currency }}</h3>
                                <small
                                    class="@if($weekly->level == 'up') text-success @elseif($weekly->level == 'down') text-danger @else text-secondary @endif fw-semibold">
                                    @if($weekly->level == 'up')
                                        <i class='bx bx-up-arrow-alt'></i>
                                    @elseif($weekly->level == 'down')
                                        <i class='bx bx-down-arrow-alt'></i>
                                    @else
                                        ~
                                    @endif
                                    {{ $weekly->difference }}%</small>
                            </div>
                        </div>
                    </div>
                @endif
                @if(!empty($monthly))
                    <div class="col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{asset('res/img/icons/unicons/cube-secondary.png')}}" alt="cube"
                                             class="rounded">
                                    </div>
                                </div>
                                <span class="d-block mb-1">{{ __('Monthly Revenue') }}</span>
                                <h3 class="card-title text-nowrap mb-2">{{ $monthly->amount }} {{ $settings->currency }}</h3>
                                <small
                                    class="@if($monthly->level == 'up') text-success @elseif($monthly->level == 'down') text-danger @else text-secondary @endif fw-semibold">
                                    @if($monthly->level == 'up')
                                        <i class='bx bx-up-arrow-alt'></i>
                                    @elseif($monthly->level == 'down')
                                        <i class='bx bx-down-arrow-alt'></i>
                                    @else
                                        ~
                                    @endif
                                    {{ $monthly->difference }}%</small>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- </div>
              <div class="row"> -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                    <div class="card-title">
                                        <h5 class="text-nowrap mb-2">{{ __('Global Report') }}</h5>
                                        <span class="badge bg-label-warning rounded-pill">{{ __('Year') }} {{$global->year}}</span>
                                    </div>
                                    <div class="mt-sm-auto">
                                        <small
                                            class="@if($global->level == 'up') text-success @elseif($global->level == 'down') text-danger @else text-secondary @endif text-nowrap fw-semibold">
                                            @if($global->level == 'up')
                                                <i class='bx bx-chevron-up'></i>
                                            @elseif($global->level == 'down')
                                                <i class='bx bx-chevron-down'></i>
                                            @else
                                                ~
                                            @endif
                                            {{ $global->difference }}%</small>
                                        <h3 class="mb-0">{{ $global->amount }} {{ $settings->currency }}</h3>
                                    </div>
                                </div>
                                <div id="activityChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mainStatistics">
        <!-- Table Statistics -->
        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">{{ __('Most Popular Packages') }}</h5>
                        <small class="text-muted">{{$topPackages->totalSales}} {{ $settings->currency }} {{ __('Total Sales') }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <h2 class="mb-2">{{$topPackages->revenue}} {{ $settings->currency }}</h2>
                            <span>{{ __('Revenue from the Top 5 Packages') }}</span>
                        </div>
                        <div id="orderStatisticsChart"></div>
                    </div>
                    <ul class="p-0 m-0">
                        @foreach($topPackages->packages as $package)
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                  <span class="avatar-initial rounded bg-label-primary">
                                    <img src="{{ 'https://' . $_SERVER['HTTP_HOST'] . '/img/items/' . $package['image'] }}"
                                         style="padding: 3px;"
                                         alt="{{ $package['package'] }}"
                                         onerror="this.src='{{ asset('res/img/question-icon.png') }}';">
                                  </span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $package['package'] }}</h6>
                                        <small class="text-muted">{{ $package['category_name'] }}</small>
                                    </div>
                                    <div class="user-progress">
                                        <small
                                            class="fw-semibold">{{ $package['total_value'] }} {{ $settings->currency }}</small>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!--/ Table Statistics -->

        <!-- Expense Overview -->
        <div class="col-md-6 col-lg-4 order-1 mb-4 mainStatistics">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">{{ __('Sales by Countries') }}</h5>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="salesByCountry" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesByCountry">
                            <a class="dropdown-item" href="javascript:void(0);">{{ __('Refresh') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @foreach($topCountries as $country)
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <img src="{{ "https://flagsapi.com/$country->code/flat/64.png" }}"
                                         alt="Country Flag"
                                         class="rounded"
                                         onerror="this.src='{{ asset('res/img/question-icon.png') }}';">
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <div class="d-flex align-items-center">
                                            <h6 class="mb-0 me-1">{{ $country->month_value }} {{ $settings->currency }}</h6>
                                            <small
                                                class="@if ($country->level == 'up') text-success @elseif($country->level == 'down') text-danger @else text-secondary @endif fw-semibold">
                                                @if ($country->level == 'up')
                                                    <i class='bx bx-up-arrow-alt'></i>
                                                @elseif ($country->level == 'down')
                                                    <i class='bx bx-down-arrow-alt'></i>
                                                @else
                                                    ~
                                                @endif
                                                {{ $country->difference }}%</small>
                                        </div>
                                        <small class="text-muted">{{ $country->country }} / Last month</small>
                                    </div>
                                    <div class="user-progress">
                                        <h6 class="mb-0">{{ $country->total_value }} {{ $settings->currency }}</h6>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!--/ Expense Overview -->

        <!-- Transactions -->
        <div class="col-md-6 col-lg-4 order-2 mb-4 mainStatistics">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">{{ __('Income by Players') }}</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                            <a class="dropdown-item" href="javascript:void(0);" id="last-days">{{ __('Last 28 Days') }}</a>
                            <a class="dropdown-item" href="javascript:void(0);" id="last-month">{{ __('Last Month') }}</a>
                            <a class="dropdown-item" href="javascript:void(0);" id="last-year">{{ __('Last Year') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0" id="players">
                        @foreach($topPlayers as $player)
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <img src="{{ $player['image'] }}" alt="User" class="rounded">
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $player['username'] }}</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-1">
                                        <h6 class="mb-0">{{ $player['total_value'] }}</h6> <span
                                            class="text-muted">{{ $player['currency'] }}</span>
                                        <h6 class="mb-0"><span class="text-muted">(</span>{{ $player['total_records'] }}
                                            <span class="text-muted"> {{ __('Transactions') }})</span></h6>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!--/ Transactions -->
        <!-- By Categories -->
        <div class="col-md-12 col-lg-6 order-4 order-lg-3">
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Purchases') }}</th>
                            <th>{{ __('Profit') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($topCategories as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-column">
                                            <h6 class="text-muted">{{$loop->iteration}}</h6>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('img/categories/' . $category->img) }}"
                                             alt="{{ $category->name }}"
                                             height="32" width="32" class="mb-3" style="margin-right: 10px;"
                                             onerror="this.src='{{ asset('res/img/question-icon.png') }}';">
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold lh-1">{{$category->name}}</span>
                                            <h6 class="text-muted">{{ __('Category') }}</h6>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="text-muted lh-1"><span
                                            class="text-primary fw-semibold">{{$category->total_records}}</span></div>
                                    <small class="text-muted">{{ __('Purchases') }}</small>
                                </td>
                                <td>
                                    <span
                                        class="text-primary fw-semibold">{{$category->total_value}} {{ $settings->currency }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('categories.view',$category->id) }}" class="btn p-0"><i class="bx bx-show"></i></a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--/ By Categories -->
        <!-- By Server -->
        <div class="col-md-12 col-lg-6 order-4 order-lg-3 ">
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Server') }}</th>
                            <th>{{ __('Purchases') }}</th>
                            <th>{{ __('Profit') }}</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($topServers as $server)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-column">
                                            <h6 class="text-muted">{{$loop->iteration}}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold lh-1">{{ $server['name'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted lh-1"><span
                                            class="text-primary fw-semibold">{{ $server['total_records'] }}</span></div>
                                    <small class="text-muted">{{ __('Purchases') }}</small>
                                </td>
                                <td>
                                    <span
                                        class="text-primary fw-semibold">{{ $server['total_value'] }} {{ $settings->currency }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--/ By Server -->
    </div>
@endsection

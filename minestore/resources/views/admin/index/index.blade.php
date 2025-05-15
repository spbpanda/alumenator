@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/apex-charts/apex-charts.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('res/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('res/js/forms-extras.js')}}"></script>
<script src="{{asset('res/js/cards-advance.js')}}"></script>
<script src="{{asset('res/js/config/charts.js?v=2')}}"></script>
@endsection

@section('page-script')
<script>
    const totalIncomeEl = document.querySelector('#yearlyChart');
    const totalIncomeConfig = {
      chart: {
        height: 220,
        type: 'area',
        toolbar: false,
        dropShadow: {
          enabled: true,
          top: 14,
          left: 2,
          blur: 3,
          color: config.colors.primary,
          opacity: 0.15
        }
      },
      series: [
        {
          name: "Monthly total",
          data: {!! json_encode($yearlyTotal->currentYear->data) !!}
        }
      ],
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 3,
        curve: 'straight'
      },
      colors: [config.colors.primary],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          shadeIntensity: 0.8,
          opacityFrom: 0.7,
          opacityTo: 0.25,
          stops: [0, 95, 100]
        }
      },
      grid: {
        show: true,
        borderColor: borderColor,
        padding: {
          top: -15,
          bottom: -10,
          left: 0,
          right: 0
        }
      },
      xaxis: {
        categories: {!! json_encode($yearlyTotal->currentYear->categories) !!},
        labels: {
          offsetX: 0,
          style: {
            colors: labelColor,
            fontSize: '13px'
          }
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        lines: {
          show: false
        }
      },
      yaxis: {
        labels: {
          offsetX: -15,
          formatter: function (val) {
            // return '$' + parseInt(val / 1000) + 'k';
            return val + ' ' + '{!! $currency !!}';
          },
          style: {
            fontSize: '13px',
            colors: labelColor
          }
        },
        // min: 1000,
        // max: 6000,
        // tickAmount: 5
      }
    };
    if (typeof totalIncomeEl !== undefined && totalIncomeEl !== null) {
      const totalIncome = new ApexCharts(totalIncomeEl, totalIncomeConfig);
      totalIncome.render();
    }
    let data = {!! json_encode($monthDailyPayments->data) !!};

    const orderAreaChartEl = document.querySelector('#monthChart'),
    orderAreaChartConfig = {
      chart: {
        height: 80,
        type: 'area',
        toolbar: {
          show: false
        },
        sparkline: {
          enabled: true
        }
      },
      markers: {
        size: 6,
        colors: 'transparent',
        strokeColors: 'transparent',
        strokeWidth: 4,
        discrete: [
          {
            fillColor: cardColor,
            seriesIndex: 0,
            dataPointIndex: data.length - 1,
            strokeColor: config.colors.success,
            strokeWidth: 2,
            size: 6,
            radius: 8
          }
        ],
        hover: {
          size: 7
        }
      },
      grid: {
        show: false,
        padding: {
          right: 8
        }
      },
      colors: [config.colors.success],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          shadeIntensity: 0.8,
          opacityFrom: 0.8,
          opacityTo: 0.25,
          stops: [0, 85, 100]
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 2,
        curve: 'smooth'
      },
      series: [
        {
          name: "Daily total",
          data: data
        }
      ],
      xaxis: {
        show: false,
        lines: {
          show: false
        },
        labels: {
          show: false
        },
        stroke: {
          width: 0
        },
        axisBorder: {
          show: false
        }
      },
      yaxis: {
        stroke: {
          width: 0
        },
        show: false
      }
    };
    if (typeof orderAreaChartEl !== undefined && orderAreaChartEl !== null) {
      const orderAreaChart = new ApexCharts(orderAreaChartEl, orderAreaChartConfig);
      orderAreaChart.render();
    }

    const day = '{{ now()->shortDayName }}';

    const visitorBarChartEl = document.querySelector("#visitorsChart"),
        visitorBarChartConfig = {
            chart: {
                height: 120,
                width: 200,
                parentHeightOffset: 0,
                type: "bar",
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    barHeight: "75%",
                    columnWidth: "40px",
                    startingShape: "rounded",
                    endingShape: "rounded",
                    borderRadius: 5,
                    distributed: true
                }
            },
            grid: {
                show: false,
                padding: {
                    top: -25,
                    bottom: -12
                }
            },
            colors: [
                (day === "Mon") ? config.colors.primary : config.colors_label.primary,
                (day === "Tue") ? config.colors.primary : config.colors_label.primary,
                (day === "Wed") ? config.colors.primary : config.colors_label.primary,
                (day === "Thu") ? config.colors.primary : config.colors_label.primary,
                (day === "Fri") ? config.colors.primary : config.colors_label.primary,
                (day === "Sat") ? config.colors.primary : config.colors_label.primary,
                (day === "Sun") ? config.colors.primary : config.colors_label.primary
            ],
            dataLabels: {
                enabled: false
            },
            series: [
                {
                    data: {!! json_encode($weeklyVisits->chartData->data) !!}
                }
            ],
            legend: {
                show: false
            },
            xaxis: {
                categories: {!! json_encode($weeklyVisits->chartData->categories) !!},
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: "13px"
                    }
                }
            },
            yaxis: {
                labels: {
                    show: false
                }
            },
            responsive: [
                {
                    breakpoint: 1440,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 9,
                                columnWidth: "60%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 1300,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 9,
                                columnWidth: "60%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 1200,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 8,
                                columnWidth: "50%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 1040,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 8,
                                columnWidth: "50%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 991,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 8,
                                columnWidth: "50%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 420,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 8,
                                columnWidth: "50%"
                            }
                        }
                    }
                }
            ]
        };
    if (typeof visitorBarChartEl !== undefined && visitorBarChartEl !== null) {
        const visitorBarChart = new ApexCharts(visitorBarChartEl, visitorBarChartConfig);
        visitorBarChart.render();
    }

    $(function() {
        $("#upgradeCheck").on('click', function(e){
            toastr.success("{{ __('Success!') }}", "{{ __('No updated were found!') }}");
        });
        $("#upgrade").on('click', function(e){
            e.preventDefault();

            Swal.fire({
                title: "{{ __('Are you sure you want to upgrade the webstore?') }}",
                text: "{{ __('It might overwrite your theme files and other files that you modified. However, we will create a backup of your current version in the site path.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('Yes, upgrade now!') }}",
                customClass: {
                    confirmButton: 'btn btn-primary me-1',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.value) {
                    $(this).slideUp(e);

                    var oldToastrOptions = toastr.options;
                    toastr.options = {
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "showDuration": "0",
                        "hideDuration": "0",
                        "timeOut": "0",
                        "extendedTimeOut": "0",
                    };

                    $(this).prop('disabled', true);
                    toastr.warning("{{ __('Do not refresh page!') }}", "{{ __('Upgrading MineStoreCMS...') }}");

                    $.ajax({
                        method: "POST",
                        url: "/admin/upgrade",
                        data: {},
                    }).done(function( msg ) {
                        Swal.fire({
                            icon: 'success',
                            title: "{{ __('Updated!') }}",
                            text: "{{ __('MineStoreCMS updated successfully!') }}",
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                        });

                        location.reload();
                    });
                }
            });
        });
    });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
    <span class="text-muted fw-light">{{ __('Home') }}</span>
</h4>

@if($ableToSeeStats)
<div class="row">
  <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <h5 class="mb-0">{{ __('Store Visitors') }}</h5>
            <small>{{ __('This Week') }}</small>
          </div>
          <div class="d-flex justify-content-between">
            <div class="mt-auto">
              <h2 class="mb-2">{{$weeklyVisits->amount}}</h2>
                <small class="@if($weeklyVisits->level == 'up') text-success @elseif($weeklyVisits->level == 'down') text-danger @else text-secondary @endif fw-semibold">
                    @if($weeklyVisits->level == 'up')
                        <i class='bx bx-up-arrow-alt'></i>
                    @elseif($weeklyVisits->level == 'down')
                        <i class='bx bx-down-arrow-alt'></i>
                    @else
                        ~
                    @endif
                        {{ $weeklyVisits->difference }}%</small>
            </div>
            <div id="visitorsChart"></div>
          </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{asset('res/img/icons/unicons/chart-success.png')}}" alt="chart success" class="rounded">
          </div>
        </div>
        <span class="fw-semibold d-block mb-1">{{ __('Total in Sales') }}</span>
        <h4 class="card-title mb-2">{{$totalAllTime}} {{ $settings->currency }}</h4>
		    <hr>
        <small class="text fw-semibold"><i class='bx bx-time-five'></i> {{ __('All Time Sales') }}</small>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body mb-2 pb-0">
			<span class="d-block fw-semibold mb-2">{{ now()->monthName }}'s {{ __('Sales') }}</span>
			<div class="row">
				<div class="col-md-8">
					<h3 class="card-title">{{$totalThisMonth->amount}} {{ $settings->currency }}</h3>
				</div>
				<div class="col-md-4">
                    <small class="@if($totalThisMonth->level == 'up') text-success @elseif($totalThisMonth->level == 'down') text-danger @else text-secondary @endif fw-semibold">
                        @if($totalThisMonth->level == 'up')
                            <i class='bx bx-up-arrow-alt'></i>
                        @elseif($totalThisMonth->level == 'down')
                            <i class='bx bx-down-arrow-alt'></i>
                        @else
                            ~
                        @endif
                        {{ $totalThisMonth->difference }}%</small>
				</div>
			</div>
      </div>
      <div id="monthChart" class="mb-3"></div>
    </div>
  </div>
  <div class="col-lg-2 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{asset('res/img/icons/unicons/computer.png')}}" alt="chart success" class="rounded">
          </div>
        </div>
        <span class="fw-semibold d-block mb-1">{{ __('Today Sales') }}</span>
        <h4 class="card-title mb-2">{{$dailyTotal}} {{ $settings->currency }}</h4>
		    <hr>
        <small class="text fw-semibold"><i class='bx bx-time-five'></i> {{ __('Today') }}</small>
      </div>
    </div>
  </div>
</div>
@else
    <div class="alert alert-primary" role="alert">
        <h4 class="alert-heading">{{ __('Warning!') }}</h4>
        <p>{{ __('You do not have access to the dashboard statistics. Please contact the administrator to get access.') }}</p>
    </div>
@endif

<div class="row">
  <div class="col-md-6 col-12 mb-4">
      @if($ableToUpdate)
	  <div class="card mb-4">
      <div class="d-flex align-items-center row">
        <div class="col-sm-4 text-center text-sm-left">
          <div class="card-body card-separator">
            <i class="fa text-primary fa-{{ $isUpdate ? 'warning' : 'check'}} fa-7x"></i>
          </div>
        </div>
        <div class="col-sm-8">
          <div class="card-body">
            @if($isUpdate)
            <h5 class="card-title">{{ __('Welcome back') }} 👋</h5>
            <p class="mb-4">{{ __('The update was found for MineStoreCMS. Do you want to upgrade to the latest version now?') }} 🤔</p>
    			  <div class="row">
      				<div class="d-grid gap-2 col-lg-12 mx-auto">
      					<button type="button" class="btn btn-primary" id="upgrade">
      						<span class="tf-icons bx bx-download me-1"></span>{{ __('Upgrade MineStore') }}
      					</button>
      				</div>
      			</div>
            @else
            <h5 class="card-title">{{ __('Welcome back') }} 👋</h5>
            <p class="mb-4">{{ __('You are using the latest MineStoreCMS web-application version. You can always check for updates.') }}</p>
            <div class="row">
              <div class="d-grid gap-2 col-lg-12 mx-auto">
                <button type="button" class="btn btn-primary" id="upgradeCheck">
                  <span class="tf-icons bx bx-refresh me-1"></span>{{ __('Check for Updates') }}
                </button>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    @else
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">{{ __('Warning!') }}</h4>
            <p>{{ __('You do not have access to the dashboard update. Please contact the administrator to get access.') }}</p>
        </div>
    @endif
    @if($ableToSeeStats)
    <div class="card mb-4">
      <div class="row row-bordered g-0">
        <div class="col-md-12">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Total Income') }}</h5>
            <small class="card-subtitle">{{ __('Yearly report overview') }}</small>
          </div>
          <div class="card-body">
            <div id="yearlyChart"></div>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>

@if($ableToSeeStats)
<div class="col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ __('Recent Payments') }}</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="recentPayments" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentPayments">
            <a class="dropdown-item" href="{{ route('payments.index') }}">{{ __('View more') }}</a>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-borderless table-striped table-hover">
          <thead>
            <tr>
			  <th>#</th>
              <th>{{ __('Username') }}</th>
              <th>{{ __('Price') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('View') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($paymentsLatest as $payment)
            <tr>
      		  <td>
                <small class="fw-semibold">{{ $payment->id }}</small>
              </td>
              <td>
                <div class="d-flex justify-content-start align-items-center">
                  <div class="avatar me-2">
                    <img
                        src="https://mc-heads.net/avatar/{{ $payment->username }}/25"
                        alt="Avatar"
                        class="rounded"
                        onerror="this.src='{{ asset('res/img/question-icon.png') }}';">
                  </div>
                  <div class="d-flex flex-column">
                    <h6 class="mb-0 text-truncate">{{ $payment->username }}</h6>
                  </div>
                </div>
              </td>
              <td>{{ $payment->price }} {{ $payment->currency }}</td>
              <td class="text-center">
				@if ($payment->status === \App\Models\Payment::PAID || $payment->status === \App\Models\Payment::COMPLETED)
					<span class="badge bg-success w-100">{{ __('COMPLETED') }}</span>
                @elseif ($payment->status === \App\Models\Payment::ERROR)
                    <span class="badge bg-danger w-100">{{ __('ERROR') }}</span>
                @elseif ($payment->status === \App\Models\Payment::CHARGEBACK)
                    <span class="badge bg-danger w-100">{{ __('CHARGEBACK') }}</span>
                @else
                    <span class="badge bg-warning w-100">{{ __('PENDING') }}</span>
                @endif
			  </td>
              <td>
                <a href="{{ route('payments.show', $payment->id) }}" target="_blank" class="btn rounded-pill btn-icon btn-primary">
                    <span class="tf-icons bx bx-show"></span>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endif
</div>
@endsection

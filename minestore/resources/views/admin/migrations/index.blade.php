@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <style>
        button.btn-table:not(:disabled),button.btn-table:disabled,
        button.btn-table[type=button]:not(:disabled),
        button.btn-table[type=reset]:not(:disabled),
        button.btn-table[type=submit]:not(:disabled) {
            cursor: pointer;
            background: 0;
            border: 0;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-primary alert-dismissible" role="alert">
            <h5 class="alert-heading d-flex align-items-center mb-1">{{ __('Well done') }} 👍</h5>
            <p class="mb-0">{{ session('success') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <h5 class="alert-heading d-flex align-items-center mb-1">{{ __('Oh snap!') }} 😱</h5>
            <p class="mb-0">{{ session('error') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif

    @if (count($migrations) === 0)
        <h4 class="fw-bold py-3 mb-1">
            <span class="text-body fw-light">{{ __('Platform Migrations') }}</span>
        </h4>
        <div class="col-12 mb-4">
            <div class="card">
                <div class="row text-center">
                    <div class="card-body mt-2 mb-3">
                        <i class="bx bx-cloud-download p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
                        <p class="card-text mb-2">
                            {{ __('Platform Migrations are used to help you migrate data from another platform to MineStoreCMS. You can sync data about categories, packages and payments.') }}
                        </p>
                        <a href="{{ route('migrations.create') }}" class="btn btn-primary btn-lg mt-2"><span class="tf-icon bx bx-plus bx-xs"></span> {{ __('New Platform Migration') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-12 mb-4">
            <div class="col-12 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="text-body fw-light mb-0">
                            {{ __('Platform Migrations') }}
                        </h4>
                    </div>
                    <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                        <a href="{{ route('migrations.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                            <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                            {{ __('New Platform Migration') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Platform') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Updated At') }}</th>
                            <th>{{ __('Started At') }}</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach ($migrations as $migration)
                            <tr id="tableItem{{ $migration->id }}">
                                <td><strong>{{ $migration->id }}</strong></td>
                                <td>{{ ucfirst($migration->platform_name) }}</td>
                                <td>
                                    @if ($migration->status == \App\Models\PlatformMigration::STATUS_CREATED)
                                        <span class="badge bg-info">{{ __('Pending') }}</span>
                                    @elseif ($migration->status == \App\Models\PlatformMigration::STATUS_PENDING)
                                        <span class="badge bg-warning">{{ __('In Progress') }}</span>
                                    @elseif ($migration->status == \App\Models\PlatformMigration::STATUS_COMPLETED)
                                        <span class="badge bg-success">{{ __('Completed') }}</span>
                                    @else
                                        <span class="badge bg-label-danger">{{ __('Failed') }}</span>
                                    @endif
                                </td>
                                <td>{{ $migration->updated_at->format('jS \of F Y H:i') }}</td>
                                <td>{{ $migration->updated_at->format('jS \of F Y H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <style>
        .CodeMirror {resize: vertical;}
    </style>
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('MineStore Frontend Service Logs') }}</span>
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">{{ __('Logs') }}</h5>
                <div class="card-body">
                    <code>
                        <pre id="logs" style="max-height: 800px;overflow-y: scroll;">
                            {{ $logs }}
                        </pre>
                    </code>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layout')

@section('content')
        @csrf
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">View your current tickets</h4>
        </div>
        <div class="card-content">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('Username')</th>
                        <th>Topic</th>
                        <th>Priority</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Time')</th>
                        <th>@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><img  width="20" height="20" src="https://minotar.net/avatar/root/25" alt="root"> root</td>
                            <td>Payment error</td>
                            <td>
                            <div class="badge_success">LOW</div>
                            <div class="badge_pending">MEDIUM</div>
                            <div class="badge_error">URGENT</div>
                            </td>
                            <td>
                                    <div class="badge_success">RESOLVED</div>
                                    <div class="badge_error">REQUIRES RESPONSE</div>
                                    <div class="badge_pending">@lang('PENDING')</div>
                            </td>
                            <td>05.05.2021</td>
                            <td class="td-actions">
                                <a href="/view/1" rel="tooltip" class="btn btn-info btn-simple btn-icon" data-original-title="@lang('View')">
                                    <i class="material-icons">mode_edit</i>
                                </a>
                                <a href="/view/1" rel="tooltip" class="btn btn-info btn-simple btn-icon" data-original-title="Mark as completed">
                                    <i class="material-icons">done</i>
                                </a>
                                <a href="/delete/1" rel="tooltip" class="btn btn-info btn-simple btn-icon" data-original-title="Delete payment">
                                    <i class="material-icons">close</i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="text-center">
                <div class="btn-group" role="group">
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-responsive/datatables.responsive.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-buttons/datatables-buttons.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.js')}}"></script>
    <script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
    <script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('js/modules/bans.js')}}"></script>
    <script type="text/javascript">
        let isBanned = {{ $ban != null ? 'true' : 'false' }};
        let banId = {{ $ban->id ?? 'undefined' }};

        $("#ban-button").click(function() {
            if (isBanned && banId !== undefined) {
                unbanUser(banId).done(function(r) {
                    isBanned = false;
                    banId = undefined;
                    toastr.success("{{ __('Customer successfully unbanned!') }}");
                    switchMainBanButton($("#ban-button"));
                }).fail(function(r) {
                    if (r.status === 410) {
                        toastr.error(r.responseJSON.message);
                    } else {
                        toastr.error("{{ __('Unable to unban this customer!') }}");
                    }
                });
            } else {
                banUser("{{ $customer->username }}").done(function(r) {
                    isBanned = true;
                    banId = r.id;
                    toastr.success("{{ __('Customer successfully banned!') }}");
                    switchMainBanButton($("#ban-button"));
                }).fail(function(r) {
                    if (r.status === 410) {
                        toastr.error(r.responseJSON.message);
                    } else {
                        toastr.error("{{ __('Unable to ban this customer!') }}");
                    }
                });
            }
        });

        // Packages Datatable
        var dt_packages_table = $('#datatables-packages');
        if (dt_packages_table.length) {
            var dt_packages = dt_packages_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("customers.purchased-packages", $customer->id) }}',
                searching: true,
                ordering: true,
                columns: [
                    { data: 'id' },
                    { data: 'package_name' },
                    { data: 'status' },
                    { data: 'type' },
                    { data: 'expiration_at' },
                    { data: 'purchased_at' },
                ],
                order: [[5, 'desc']],
                columnDefs: [
                    {
                        targets: 0, // ID
                        render: function (data, type, full, meta) {
                            return full['id'];
                        },
                    },
                    {
                        targets: 1, // Package
                        render: function (data, type, full, meta) {
                            var packageName = full['package_name'];
                            var packageImage = full['package_image'];
                            var itemId = full['id'];
                            var output = '<span class="fw-semibold">' + packageName + '</span>';

                            var link = '<a href="/admin/items/' + itemId + '" class="text-decoration-none text-dark">' + output + '</a>';

                            if (packageImage) {
                                link =
                                    '<div class="d-flex justify-content-start align-items-center">' +
                                    '<div class="avatar-wrapper me-2">' +
                                    '<a href="/admin/items/' + itemId + '">' +
                                    '<img src="' + packageImage + '" alt="' + packageName + '" class="rounded" style="width: 25px; height: 25px;" onerror="this.style.display=\'none\';">' +
                                    '</a>' +
                                    '</div>' +
                                    '<a href="/admin/items/' + itemId + '" class="text-decoration-none text-lighter">' + output + '</a>' +
                                    '</div>';
                            }

                            return link;
                        },
                    },
                    {
                        targets: 2, // Status
                        render: function (data, type, full, meta) {
                            var status = full['status'];
                            var statusMap = {
                                '0': { text: "{{ __('PENDING') }}", class: 'warning' },
                                '1': { text: "{{ __('COMPLETED') }}", class: 'success' },
                                '2': { text: "{{ __('ERROR') }}", class: 'danger' },
                                '3': { text: "{{ __('COMPLETED') }}", class: 'success' },
                                '4': { text: "{{ __('CHARGEBACK') }}", class: 'danger' },
                                '5': { text: "{{ __('REFUNDED') }}", class: 'secondary' },
                            };

                            var $output = '<span class="badge bg-' + statusMap[status].class + ' w-100">' + statusMap[status].text + '</span>';
                            return $output;
                        },
                    },
                    {
                        targets: 3, // Type
                        render: function (data, type, full, meta) {
                            var type = full['type'];
                            var typeClass = type === '1' ? 'primary' : 'secondary';
                            var typeName = type === '1' ? "{{ __('Subscription') }}" : "{{ __('One-time') }}";

                            return '<span class="badge w-100 bg-' + typeClass + '">' + typeName + '</span>';
                        },
                    },
                    {
                        targets: 4, // Expiration
                        render: function (data, type, full, meta) {
                            if (!full['expiration_at']) {
                                return '<span class="text-muted">-</span>';
                            }

                            var expirationDate = moment(full['expiration_at']);
                            var now = moment();
                            var isExpired = expirationDate.isBefore(now);

                            var formattedDate = expirationDate.format('DD.MM.YYYY HH:mm');
                            var className = isExpired ? 'text-danger' : 'text-success';

                            return '<span class="' + className + '">' + formattedDate + '</span>';
                        },
                    },
                    {
                        targets: 5, // Purchased At
                        render: function (data, type, full, meta) {
                            var purchaseDate = moment(full['purchased_at']).format('DD.MM.YYYY HH:mm');
                            var humanDate = full['purchased_at'];

                            return '<span title="' + purchaseDate + '">' + humanDate + '</span>';
                        },
                    }
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row).attr('id', 'packageItem' + data['id']);
                },
            });
        }

        // Transactions Datatable
        var dt_transactions_table = $('#datatables-transactions');
        if (dt_transactions_table.length) {
            var dt_transactions = dt_transactions_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("customers.get-transactions", $customer->id) }}',
                searching: true,
                ordering: true,
                columns: [
                    { data: 'id' },
                    { data: 'items' },
                    { data: 'price' },
                    { data: 'status' },
                    { data: 'created_at' },
                ],
                order: [[4, 'desc']],
                columnDefs: [
                    {
                        targets: 0, // ID
                        render: function (data, type, full, meta) {
                            return full['id'];
                        },
                    },
                    {
                        targets: 1, // Packages
                        render: function (data, type, full, meta) {
                            var items = full['items'] || [];
                            var output = '';

                            items.forEach(function (item) {
                                var packageName = item['package_name'];
                                var packageImage = item['package_image'];
                                var itemId = item['item_id'];

                                var itemHtml = '<span class="fw-semibold">' + packageName + '</span>';
                                var link = '<a href="/admin/items/' + itemId + '" class="text-decoration-none text-dark">' + itemHtml + '</a>';

                                if (packageImage) {
                                    link =
                                        '<div class="d-flex justify-content-start align-items-center mb-2">' +
                                        '<div class="avatar-wrapper me-2">' +
                                        '<a href="/admin/items/' + itemId + '">' +
                                        '<img src="' + packageImage + '" alt="' + packageName + '" class="rounded" style="width: 25px; height: 25px;" onerror="this.style.display=\'none\';">' +
                                        '</a>' +
                                        '</div>' +
                                        '<a href="/admin/items/' + itemId + '" class="text-decoration-none text-lighter">' + itemHtml + '</a>' +
                                        '</div>';
                                } else {
                                    link = '<div class="mb-2">' + link + '</div>';
                                }

                                output += link;
                            });

                            return output || 'No items';
                        },
                    },
                    {
                        targets: 2, // Total
                        render: function (data, type, full, meta) {
                            return '<span class="fw-semibold">' + full['price'] + ' {{ $currency ?? 'USD' }}</span>';
                        },
                    },
                    {
                        targets: 3, // Status
                        render: function (data, type, full, meta) {
                            var status = full['status'];
                            var statusMap = {
                                '0': { text: "{{ __('PENDING') }}", class: 'warning' },
                                '1': { text: "{{ __('COMPLETED') }}", class: 'success' },
                                '2': { text: "{{ __('ERROR') }}", class: 'danger' },
                                '3': { text: "{{ __('COMPLETED') }}", class: 'success' },
                                '4': { text: "{{ __('CHARGEBACK') }}", class: 'danger' },
                                '5': { text: "{{ __('REFUNDED') }}", class: 'secondary' },
                            };

                            var $output = '<span class="badge bg-' + statusMap[status].class + ' w-100">' + statusMap[status].text + '</span>';
                            return $output;
                        },
                    },
                    {
                        targets: 4, // Created At
                        render: function (data, type, full, meta) {
                            var createdDate = moment(full['created_at']).format('DD.MM.YYYY HH:mm');
                            var humanDate = full['human_date'];

                            return '<span title="' + createdDate + '">' + humanDate + '</span>';
                        },
                    },
                    {
                        targets: 5, // Actions
                        render: function(data, type, full, meta) {
                            return '<div class="d-flex justify-content-center">' +
                                '<a href="{{ route('payments.show', ['payment' => ':id']) }}'.replace(':id', full['id']) +
                                '" class="btn btn-sm btn-icon"><i class="bx bx-show text-primary"></i></a>' +
                                '</div>';
                        }
                    }
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row).attr('id', 'transactionItem' + data['id']);
                },
            });
        }

        // Subscriptions Datatable
        var dt_subscriptions_table = $('#datatables-subscriptions');
        if (dt_subscriptions_table.length) {
            var dt_subscriptions = dt_subscriptions_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("customers.get-subscriptions", $customer->id) }}',
                searching: true,
                ordering: true,
                columns: [
                    { data: 'id' },
                    { data: 'package_name' },
                    { data: 'status' },
                    { data: 'billing_cycles' },
                    { data: 'renewal_date' },
                    { data: 'created_at' },
                    { data: null, orderable: false, searchable: false }
                ],
                order: [[5, 'desc']],
                columnDefs: [
                    {
                        targets: 0, // ID
                        render: function (data, type, full, meta) {
                            return full['id'];
                        },
                    },
                    {
                        targets: 1, // Package
                        render: function (data, type, full, meta) {
                            var packageName = full['package_name'];
                            var packageImage = full['package_image'];
                            var itemId = full['item_id'];
                            var output = '<span class="fw-semibold">' + packageName + '</span>';

                            var link = '<a href="/admin/items/' + itemId + '" class="text-decoration-none text-dark">' + output + '</a>';

                            if (packageImage) {
                                link =
                                    '<div class="d-flex justify-content-start align-items-center">' +
                                    '<div class="avatar-wrapper me-2">' +
                                    '<a href="/admin/items/' + itemId + '">' +
                                    '<img src="' + packageImage + '" alt="' + packageName + '" class="rounded" style="width: 25px; height: 25px;" onerror="this.style.display=\'none\';">' +
                                    '</a>' +
                                    '</div>' +
                                    '<a href="/admin/items/' + itemId + '" class="text-decoration-none text-lighter">' + output + '</a>' +
                                    '</div>';
                            }

                            return link;
                        },
                    },
                    {
                        targets: 2, // Status
                        render: function(data, type, full, meta) {
                            var status = full['status'];
                            var statusMap = {
                                '0': { text: "{{ __('ACTIVE') }}", class: 'success' },
                                '1': { text: "{{ __('CANCELLED') }}", class: 'danger' },
                                '2': { text: "{{ __('WAITING FOR CONFIRM') }}", class: 'warning' },
                            };
                            var $output = '<span class="badge bg-' + statusMap[status].class + ' w-100">' + statusMap[status].text + '</span>';

                            return $output;
                        },
                    },
                    {
                        targets: 3, // Billing Cycles
                        render: function (data, type, full, meta) {
                            return full['billing_cycles'] || 'N/A';
                        },
                    },
                    {
                        targets: 4,
                        render: function (data, type, full, meta) {
                            var renewalDateRaw = full['renewal_date_raw'];
                            var humanDate = full['renewal_date'];

                            if (renewalDateRaw) {
                                var formattedDate = moment(renewalDateRaw).format('Do [of] MMMM YYYY');
                                return '<span title="' + humanDate + '">' + formattedDate + '</span>';
                            }

                            return 'N/A';
                        },
                    },
                    {
                        targets: 5, // Created At
                        render: function (data, type, full, meta) {
                            var createdDate = moment(full['created_at']).format('DD.MM.YYYY HH:mm');
                            var humanDate = full['human_date'];

                            return '<span title="' + createdDate + '">' + humanDate + '</span>';
                        },
                    },
                    {
                        targets: 6, // Actions
                        render: function(data, type, full, meta) {
                            return '<div class="d-flex justify-content-center">' +
                                '<a href="{{ route('subscriptions.show', ['id' => ':id']) }}'.replace(':id', full['id']) +
                                '" class="btn btn-sm btn-icon"><i class="bx bx-show text-primary"></i></a>' +
                                '</div>';
                        }
                    }
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row).attr('id', 'subscriptionItem' + data['id']);
                },
            });
        }

        // Chargebacks Datatable
        var dt_chargebacks_table = $('#datatables-chargebacks');
        if (dt_chargebacks_table.length) {
            var dt_chargebacks = dt_chargebacks_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("customers.get-chargebacks", $customer->id) }}',
                searching: true,
                ordering: true,
                columns: [
                    { data: 'id' },
                    { data: 'sid' },
                    { data: 'status' },
                    { data: 'price' },
                    { data: 'created_at' },
                ],
                order: [[4, 'desc']],
                columnDefs: [
                    {
                        targets: 0, // ID
                        render: function (data, type, full, meta) {
                            return full['id'];
                        },
                    },
                    {
                        targets: 1, // sID
                        render: function (data, type, full, meta) {
                            return '<span class="fw-semibold">' + full['sid'] + '</span>';
                        },
                    },
                    {
                        targets: 2, // Status
                        render: function(data, type, full, meta) {
                            var status = full['status'];
                            var statusMap = {
                                '0': { text: "{{ __('PENDING') }}", class: 'warning' },
                                '1': { text: "{{ __('COMPLETED') }}", class: 'success' },
                                '2': { text: "{{ __('ERROR') }}", class: 'danger' },
                                '3': { text: "{{ __('APPROVED') }}", class: 'success' },
                                '4': { text: "{{ __('REJECTED') }}", class: 'danger' },
                                '5': { text: "{{ __('UNDER REVIEW') }}", class: 'info' },
                            };
                            var $output = '<span class="badge bg-' + (statusMap[status] ? statusMap[status].class : 'secondary') + ' w-100">' +
                                (statusMap[status] ? statusMap[status].text : status) + '</span>';

                            return $output;
                        },
                    },
                    {
                        targets: 3, // Price
                        render: function (data, type, full, meta) {
                            return '<span class="fw-semibold">' + (full['price'] || '0.00') + '</span>';
                        },
                    },
                    {
                        targets: 4, // Created At
                        render: function (data, type, full, meta) {
                            var createdDate = moment(full['created_at']).format('DD.MM.YYYY HH:mm');
                            var humanDate = full['human_date'];

                            return '<span title="' + createdDate + '">' + humanDate + '</span>';
                        },
                    },
                    {
                        targets: 5, // Actions
                        render: function(data, type, full, meta) {
                            var $row_output =
                                '<a href="{{ route('chargeback.show', '/') }}/' + full['id'] + '" class="btn btn-sm text-primary btn-icon item-edit"><i class="bx bx-show"></i></a>'
                                + `<span data-id="${full['id']}" class="btn btn-sm text-primary btn-icon item-edit done-button"><i class="bx bxs-check-circle"></i></span>`
                                + `<span data-id="${full['id']}" class="btn btn-sm text-primary btn-icon item-edit delete-button"><i class='bx bx-trash'></i></span>`;
                            return $row_output;
                        }
                    }
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row).attr('id', 'chargebackItem' + data['id']);
                },
            });
        }
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('User Details for') }} {{ $customer->identificator }} (#{{$customer->id}})</span>
    </h4>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header mb-0 pb-2">
                        <h4 class="card-title" style="text-align: center;">{{ __('User information') }}</h4>
                    </div>
                    <hr>
                    <div class="card-body text-center">
                        <img src="https://mc-heads.net/avatar/{{ $customer->uuid ?? $customer->username }}"
                             alt="{{ $customer->username }}"
                             onerror="this.src='{{ asset('res/img/question-icon.png') }}';"
                             style="width: 155px; border-radius: 4px;transform: scale(-1, 1); margin-bottom: 5px;">
                        <h5 style="font-size: 24px; font-weight: 500;">{{ $customer->username }}</h5>
                        <h6 style="font-size: 14px;">UUID: {{ $customer->uuid ?? 'N/A' }}</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('lookup.search', $customer->username) }}" target="_blank"
                                   class="btn btn-lg btn-warning">
                                    <span class="tf-icons bx bx-search me-1"></span>
                                    {{ __('Lookup') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-2 d-flex justify-content-start">
                                <a href="{{ route('customers.index') }}" class="btn btn-primary"
                                   style="margin-right: 5px;">
                                    <span class="tf-icons bx bx-arrow-back me-1"></span>{{ __('Back') }}
                                </a>
                            </div>
                            <div class="col-md-7 mb-2 d-flex justify-content-end">
                                @if (!$customer->banned)
                                    <button type="button" class="btn btn-danger" id="ban-button">
                                        <span class="tf-icons bx bx-x-circle me-1"></span>{{ __('Ban User') }}
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success" id="ban-button">
                                        <span class="tf-icons bx bx-check-circle me-1"></span>{{ __('Unban User') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="table-responsive table-striped">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            ID:
                                        </td>
                                        <td>
                                            #{{ $customer->id }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            Status:
                                        </td>
                                        <td>
                                            @if ($customer->banned)
                                                <span class="badge bg-danger">{{ __('Banned') }}</span>
                                            @else
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            Total Spent:
                                        </td>
                                        <td>
                                            {{ $customer->total_spent }} {{ $currency ?? 'USD' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            Total Orders:
                                        </td>
                                        <td>
                                            {{ $customer->total_orders }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            Average Order Amount:
                                        </td>
                                        <td>
                                            {{ $customer->avg_spent }} {{ $currency ?? 'USD' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            Active Subscriptions:
                                        </td>
                                        <td>
                                            {{ $customer->active_subscriptions }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            First Seen At:
                                        </td>
                                        <td>
                                            {{ $customer->first_seen_at }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-center mb-2">
        <div class="col-md-6">
            <h5 class="text-body fw-light mb-0">
                {{ __('Purchased Packages') }}
            </h5>
        </div>
        <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
            <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                {{ __('Attach Package') }}
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-12 mb-4">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <table class="datatables-basic table border-top" id="datatables-packages">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('Package') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Expiration At') }}</th>
                            <th>{{ __('Purchased At') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-center mb-2">
        <div class="col-md-6">
            <h5 class="text-body fw-light mb-0">
                {{ __('Transactions List') }}
            </h5>
        </div>
        <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
            <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                {{ __('Create a Manual Transaction') }}
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-12 mb-4">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <table class="datatables-basic table border-top" id="datatables-transactions">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('Packages') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Creation Time') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-center mb-2">
        <div class="col-md-12">
            <h5 class="text-body fw-light mb-0">
                {{ __('Subscriptions History') }}
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-12 mb-4">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <table class="datatables-basic table border-top" id="datatables-subscriptions">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('Package') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Billing Cycles') }}</th>
                            <th>{{ __('Next Renewal Date') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-center mb-2">
        <div class="col-md-12">
            <h5 class="text-body fw-light mb-0">
                {{ __('Chargebacks History') }}
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-12 mb-4">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <table class="datatables-basic table border-top" id="datatables-chargebacks">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('Chargeback ID') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

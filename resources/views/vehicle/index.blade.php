@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Vehicles') }}
            </h1>
        </div>
    </div>
    <!-- END Page Header -->

    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if (Session::has($msg))
            <div class="alert alert-{{ $msg }} alert-dismissible fade-in show" role="alert">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {!! Session::get($msg) !!}
            </div>
        @endif
    @endforeach

    <div class="block">
        <div class="row">
            <div class="col-lg-12">
                <div class="block">
                    <form action="{{ route('vehicle.index') }}" method="GET">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="customer_name" class="form-control" autofocus placeholder="{{ __('Customre Name') }}" value="{{ request('customer_name') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="model" class="form-control" placeholder="{{ __('Model') }}" value="{{ request('model') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="registration_number" class="form-control" placeholder="{{ __('Registration Number') }}" value="{{ request('registration_number') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="mobile_number" class="form-control" placeholder="{{ __('Mobile Number') }}" value="{{ request('mobile_number') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-4">
                                    <fieldset>
                                        <div class="form-group">
                                            <textarea name="address" class="form-control" placeholder="{{ __('Address') }}">{{ request('address') }}</textarea>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="branch" class="form-control" placeholder="{{ __('Branch') }}" value="{{ request('branch') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="area" class="form-control" placeholder="{{ __('Area') }}" value="{{ request('area') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="region" class="form-control" placeholder="{{ __('Region') }}" value="{{ request('region') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <a href="{{ route('vehicle.index', ['page' => request('page')]) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Clear') }}"><i class="fa fa-close"></i></a>
                                    <button type="submit" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Search') }}"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="block">
        <div class="block-title row">
            <h2 class="col-md-6">
                <label class="pull-left">{{ __('Listing') }}</label>
            </h2>

            <h2 class="col-md-6">
                <label class="pull-right">
                    <form action="{{ route('vehicle.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="input-group">
                                    <input type="file" class="form-control" name="excel_import" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />

                                    <span class="input-group-btn">
                                        <button type="submit" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="{{ __('Import') }}">
                                            <i class="gi gi-file_import"></i>
                                        </button>
                                    </span>
                                </div>
                                @error('excel_import')
                                    <em class="color-red error invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </em>
                                @enderror
                            </div>

                            <div class="col-xs-4">
                                <a href="{{ route('vehicle.create') }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Add New') }}">
                                    <i class="fa fa-plus"></i> {{ __('Add New') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </label>
            </h2>
        </div>

        <p>
            <div class="table-responsive">
                <table class="table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>{{ __('Customer Name') }}</th>
                            <th>{{ __('Model') }}</th>
                            <th>{{ __('Registration Number') }}</th>
                            <th>{{ __('Mobile Number') }}</th>
                            <th>{{ __('Address') }}</th>
                            <th>{{ __('Branch') }}</th>
                            <th>{{ __('Area') }}</th>
                            <th>{{ __('Region') }}</th>
                            <th>{{ __('Confirm') }}</th>
                            <th>{{ __('Cancel') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($vehicles) && !$vehicles->isEmpty())
                            @foreach ($vehicles as $vehicle)
                                <tr>
                                    <td class="text-center">{{ $vehicle->id }}</td>
                                    <td>{{ !empty($vehicle->customer_name) ? $vehicle->customer_name : "-" }}</td>
                                    <td>{{ !empty($vehicle->model) ? $vehicle->model : "-" }}</td>
                                    <td>{{ !empty($vehicle->registration_number) ? $vehicle->registration_number : "-" }}</td>
                                    <td>{{ !empty($vehicle->mobile_number) ? $vehicle->mobile_number : "-" }}</td>
                                    <td>{{ !empty($vehicle->address) ? $vehicle->address : "-" }}</td>
                                    <td>{{ !empty($vehicle->branch) ? $vehicle->branch : "-" }}</td>
                                    <td>{{ !empty($vehicle->area) ? $vehicle->area : "-" }}</td>
                                    <td>{{ !empty($vehicle->region) ? $vehicle->region : "-" }}</td>
                                    <td>
                                        <label class="switch switch-success">
                                            <form id="confirm-vehicle-form-{{ $vehicle->id }}" action="{{ route('vehicle.confirm', $vehicle->id) }}" method="POST" class="d-none">
                                                @csrf

                                                <input type="radio" name="is_confirm" {{ $vehicle->is_confirm == $vehicle::CONFIRM ? "checked" : "" }} />
                                                <span data-toggle="tooltip" title="" class="confirm-vehicle-button" data-original-title="{{ $vehicle->is_confirm == $vehicle::NOT_CONFIRM ? __('Click To Confirm') : __('Confirmed') }}" data-id="{{ $vehicle->id }}" data-cancel="{{ $vehicle->is_cancel }}"></span>

                                                <div class="modal" id="modal-select-seizer-{{ $vehicle->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    {{ __('Sub Seizer Selection') }}
                                                                </h5>
                                                            </div>

                                                            <div class="modal-body">
                                                                <select class="form-control" name="user_id">
                                                                    <option value="">{{ __('Select Sub Seizer') }}</option>

                                                                    @if (!empty($users) && !$users->isEmpty())
                                                                        @foreach ($users as $user)
                                                                            @if (strtotime($user->currentSubscription['to']) >= $todayDate)
                                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-primary confirm-vehicle" data-id="{{ $vehicle->id }}">{{ __('Confirm') }}</button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </label>
                                    </td>
                                    <td>
                                        <label class="switch switch-danger">
                                            <form id="cancel-vehicle-form-{{ $vehicle->id }}" action="{{ route('vehicle.cancel', $vehicle->id) }}" method="POST" class="d-none">
                                                @csrf

                                                <input type="radio" name="is_cancel" {{ $vehicle->is_cancel == $vehicle::CANCEL ? "checked" : "" }} />
                                                <span data-toggle="tooltip" title="" class="cancel-vehicle-button" data-original-title="{{ $vehicle->is_cancel == $vehicle::NOT_CANCEL ? __('Click To Cancel') : 'Cancelled' }}" data-id="{{ $vehicle->id }}" data-confirm="{{ $vehicle->is_confirm }}"></span>
                                            </form>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <a href="{{ route('vehicle.edit', $vehicle->id) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Edit') }}"><i class="fa fa-pencil"></i></a>
                                            <a href="javascript:void(0);" data-toggle="tooltip" title="" class="btn btn-danger remove-button" data-original-title="{{ __('Remove') }}" data-id="{{ $vehicle->id }}"><i class="fa fa-times"></i></a>
                                            <form id="remove-form-{{ $vehicle->id }}" action="{{ route('vehicle.destroy', $vehicle->id) }}" method="POST" class="d-none">
                                                @csrf
                                                {{ method_field('DELETE') }}
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="12" class="text-center">
                                    <mark>{{ __('No record found!') }}</mark>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{ $vehicles->withQueryString()->links('pagination.default') }}
            </div>
        </p>
    </div>
@endsection
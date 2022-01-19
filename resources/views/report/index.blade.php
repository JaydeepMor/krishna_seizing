@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Vehicle Reports') }}
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

    <div class="alert alert-danger fade-in show" role="alert">
        * {{ __("Finance Company / (Confirmed or Cancelled) is mandatory for the export file.") }}
        <br />
        * {{ __('We will send an email to ') }} <a href="mailto:{{ env('VEHICLE_IMPORTED_NOTIFICATION_EMAIL', '') }}">{{ env('VEHICLE_IMPORTED_NOTIFICATION_EMAIL', '') }}</a>{{ __(" once all data exported so wait for an email.") }}<i>{{ __(' IF YOU DIDN\'T RECEIVE ANY EMAIL AFTER LONG TIME THEN CONTACT US.') }}</i>
    </div>

    <div class="block">
        <div class="row">
            <div class="col-lg-12">
                <div class="block">
                    <form action="{{ route('report.index') }}" method="GET">
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
                                            <label>&nbsp;</label>
                                            <input type="text" name="branch" class="form-control" placeholder="{{ __('Branch') }}" value="{{ request('branch') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <input type="text" name="area" class="form-control" placeholder="{{ __('Area') }}" value="{{ request('area') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <input type="text" name="region" class="form-control" placeholder="{{ __('Region') }}" value="{{ request('region') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-4">
                                    <label>&nbsp;</label>
                                    <div class="input-group">
                                        <input type="date" name="from_date" id="from_date" class="form-control" placeholder="{{ __('From Date') }}" value="{{ request('from_date') }}" />
                                        <div class="input-group-addon">{{ __('To') }}</div>
                                        <input type="date" name="to_date" id="to_date" class="form-control" placeholder="{{ __('To Date') }}" value="{{ request('to_date') }}" />
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <select name="finance_company_id" class="form-control">
                                                <option value="">{{ __("Select Finance Company") }}</option>

                                                @php
                                                    $financeCompanyName = "";
                                                @endphp

                                                @if (!empty($financeCompanies) && !$financeCompanies->isEmpty())
                                                    @foreach ($financeCompanies as $financeCompany)
                                                        @php
                                                            if (request('finance_company_id') == $financeCompany->id) {
                                                                $financeCompanyName = $financeCompany->name;
                                                            }
                                                        @endphp

                                                        <option value="{{ $financeCompany->id }}" {{ (request('finance_company_id') == $financeCompany->id) ? 'selected' : '' }}>{{ $financeCompany->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <label>&nbsp;</label>
                                        <div class="input-group form-control">
                                            <input type="radio" name="is_confirm" id="is_confirm" value="{{ $modal::CONFIRM }}" {{ request('is_confirm', null) == $modal::CONFIRM ? 'checked="true"' : (request('is_cancel', null) != $modal::CANCEL ? 'checked="true"' : '') }} />
                                            <label for="is_confirm">{{ __('Confirmed') }}</label>
                                            <input type="radio" name="is_cancel" id="is_cancel" value="{{ $modal::CANCEL }}" {{ request('is_cancel', null) == $modal::CANCEL ? 'checked="true"' : '' }} />
                                            <label for="is_cancel">{{ __('Cancelled') }}</label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <label>&nbsp;</label>
                                    <br />
                                    <a href="{{ route('report.index', ['page' => request('page')]) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Clear') }}"><i class="fa fa-close"></i></a>
                                    <button type="submit" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Search') }}"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                            <br />
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

            @if ((!empty(request('finance_company_id', null)) || !empty(request('is_confirm', null)) || !empty(request('is_cancel', null))) && !empty($vehicles) && !$vehicles->isEmpty())
                <h2 class="col-md-6">
                    <label class="pull-right">
                        <form action="{{ route('vehicles.report.export', $queryStrings) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="input-group">
                                        <span class="input-group-btn text-right">
                                            <button type="submit" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="{{ __('Export To CSV') }}">
                                                <i class="gi gi-file_export"></i>
                                            </button>
                                        </span>
                                    </div>
                                    @error('excel_export')
                                        <em class="color-red error invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </em>
                                    @enderror
                                </div>
                            </div>
                        </form>
                    </label>
                </h2>
            @endif
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
                                        {{ $vehicle->isConfirm[$vehicle->is_confirm] }}
                                    </td>
                                    <td>
                                        {{ $vehicle->isCancel[$vehicle->is_cancel] }}
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

@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Activities') }}
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
                    <form action="{{ route('activity.index') }}" method="GET">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="name" class="form-control" autofocus placeholder="{{ __('Name') }}" value="{{ request('name') }}" />
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="vehicle" class="form-control" autofocus placeholder="{{ __('Vehicle') }}" value="{{ request('vehicle') }}" />
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="latitude" class="form-control" autofocus placeholder="{{ __('Latitude') }}" value="{{ request('latitude') }}" />
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="longitude" class="form-control" autofocus placeholder="{{ __('Longitude') }}" value="{{ request('longitude') }}" />
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-xs-2">
                                    <a href="{{ route('activity.index', ['page' => request('page')]) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Clear') }}"><i class="fa fa-close"></i></a>
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
        </div>

        <p>
            <div class="table-responsive">
                <table class="table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Registration Number') }}</th>
                            <th>{{ __('Latitude') }}</th>
                            <th>{{ __('Longitude') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th class="text-center">{{ __('Map') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($vehicles) && !$vehicles->isEmpty())
                            @foreach ($vehicles as $vehicle)
                                <tr>
                                    <td class="text-center">{{ $vehicle->id }}</td>
                                    <td>{{ !empty($vehicle->name) ? $vehicle->name : "-" }}</td>
                                    <td>{{ !empty($vehicle->registration_number) ? $vehicle->registration_number : "-" }}</td>
                                    <td>{{ !empty($vehicle->latitude) ? $vehicle->latitude : "-" }}</td>
                                    <td>{{ !empty($vehicle->longitude) ? $vehicle->longitude : "-" }}</td>
                                    <td>
                                        @if (!empty($vehicle->created_at))
                                            {{ \Carbon\Carbon::parse($vehicle->created_at . 'UTC')->tz('Asia/Calcutta')->format(DEFAULT_DATE_TIME_FORMAT) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group">
                                            <a href="javascript:void(0);" data-toggle="tooltip" title="" class="btn btn-secondary show-map" data-original-title="{{ __('Show In Map') }}" data-latitude="{{ $vehicle->latitude }}" data-longitude="{{ $vehicle->longitude }}"><i class="fa fa-map"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center">
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

    <div class="modal" id="modal-activity-map" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ __('Activity Map') }}
                    </h5>
                </div>

                <div class="modal-body" id="activity-map" style="min-height: 500px !important;">
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
@endsection

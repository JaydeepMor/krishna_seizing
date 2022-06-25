@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Sub Seizers') }}
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
                    <form action="{{ route('subseizer.index') }}" method="GET">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xs-3">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="name" class="form-control" autofocus placeholder="{{ __('Name') }}" value="{{ request('name') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="imei_number" class="form-control" placeholder="{{ __('IMEI Number') }}" value="{{ request('imei_number') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-4">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="address" class="form-control" placeholder="{{ __('Address') }}" value="{{ request('address') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-3">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="contact_number" class="form-control" placeholder="{{ __('Contact Number') }}" value="{{ request('contact_number') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="from_date">{{ __('Subscription Month') }}</label>
                                            <select name="subscription_month" class="form-control">
                                                <option value="">{{ __('-- Select --') }}</option>

                                                @foreach ($pastOneYearMonths as $key => $pastOneYearMonth)
                                                    <option value="{{ $key }}" {{ $key == request('subscription_month') ? 'selected' : '' }}>{{ $pastOneYearMonth }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <label>&nbsp;</label>
                                    <br />
                                    <a href="{{ route('subseizer.index', ['page' => request('page')]) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Clear') }}"><i class="fa fa-close"></i></a>
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
                <label class="pull-right btn-group">
                    <a href="{{ route('subseizer.create') }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Add New') }}"><i class="fa fa-plus"></i> {{ __('Add New') }}</a>

                    @if (!empty($users) && !$users->isEmpty())
                        <form action="{{ route('subseizer.report.export', $queryStrings) }}" class="btn btn-default" method="POST" enctype="multipart/form-data" style="margin: 0;padding: 0;">
                            @csrf

                            <button type="submit" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="{{ __('Export To CSV') }}">
                                <i class="gi gi-file_export"></i>
                            </button>
                            @error('excel_export')
                                <em class="color-red error invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </em>
                            @enderror
                        </form>
                    @endif
                </label>
            </h2>
        </div>

        <p>
            <div class="table-responsive">
                <table class="table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('IMEI Number') }}</th>
                            <th>{{ __('Address') }}</th>
                            <th>{{ __('Contact Number') }}</th>
                            <th>{{ __('ID Proof') }}</th>
                            <th>{{ __('Selfie') }}</th>
                            <th>{{ __('Subscription From / To') }}</th>
                            <th>{{ __('Subscribe') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($users) && !$users->isEmpty())
                            @foreach ($users as $user)
                                <tr>
                                    <td class="text-center">{{ $user->id }}</td>
                                    <td>{{ !empty($user->name) ? $user->name : "-" }}</td>
                                    <td>{{ !empty($user->imei_number) ? $user->imei_number : "-" }}</td>
                                    <td>{{ !empty($user->address) ? $user->address : "-" }}</td>
                                    <td>{{ !empty($user->contact_number) ? $user->contact_number : "-" }}</td>
                                    <td>
                                        @if (!empty($user->id_proof))
                                            <a href="{{ !empty($user->id_proof) ? $user->id_proof : '#' }}" target="_blank">{{ __('View') }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($user->selfie))
                                            <a href="{{ !empty($user->selfie) ? $user->selfie : '#' }}" target="_blank">{{ __('View') }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $user->currentSubscription['from'] . ' / ' . $user->currentSubscription['to'] }}</td>
                                    <td>
                                        <label class="switch switch-danger">
                                            <form id="subscribe-user-form-{{ $user->id }}" action="{{ route('subseizer.subscription', $user->id) }}" method="POST" class="d-none">
                                                @csrf

                                                <input type="radio" name="is_subscribed" {{ $user->is_subscribed ? "checked" : "" }} />
                                                <span data-toggle="tooltip" title="" class="subscribe-user-button" data-original-title="{{ __('Click To Subscribe') }}" data-id="{{ $user->id }}" data-subscribed="{{ $user->is_subscribed }}"></span>
                                            </form>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group">
                                            <a href="{{ route('subseizer.sync.check', $user->id) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Clear Sync') }}"><i class="fa fa-circle-o-notch"></i></a>
                                            <a href="{{ route('subseizer.edit', $user->id) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Edit') }}"><i class="fa fa-pencil"></i></a>
                                            <a href="javascript:void(0);" data-toggle="tooltip" title="" class="btn btn-danger remove-button" data-original-title="{{ __('Remove') }}" data-id="{{ $user->id }}"><i class="fa fa-times"></i></a>
                                            <form id="remove-form-{{ $user->id }}" action="{{ route('subseizer.destroy', $user->id) }}" method="POST" class="d-none">
                                                @csrf
                                                {{ method_field('DELETE') }}
                                            </form>
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
                {{ $users->withQueryString()->links('pagination.default') }}
            </div>
        </p>
    </div>
@endsection

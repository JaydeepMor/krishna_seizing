@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Finance HOs') }}
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
                    <form action="{{ route('ho.index') }}" method="GET">
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
                                            <input type="text" name="vendor_code" class="form-control" placeholder="{{ __('Vendor Code') }}" value="{{ request('vendor_code') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="city_name" class="form-control" placeholder="{{ __('City Name') }}" value="{{ request('city_name') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="gst_number" class="form-control" placeholder="{{ __('Gst Number') }}" value="{{ request('gst_number') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="contact_person" class="form-control" placeholder="{{ __('Contact Person') }}" value="{{ request('contact_person') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <a href="{{ route('ho.index', ['page' => request('page')]) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Clear') }}"><i class="fa fa-close"></i></a>
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
                    <a href="{{ route('ho.create') }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Add New') }}"><i class="fa fa-plus"></i> {{ __('Add New') }}</a>
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
                            <th>{{ __('Vendor Code') }}</th>
                            <th>{{ __('City') }}</th>
                            <th>{{ __('GST Number') }}</th>
                            <th>{{ __('Contact Person') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($financeHos) && !$financeHos->isEmpty())
                            @foreach ($financeHos as $financeHo)
                                <tr>
                                    <td class="text-center">{{ $financeHo->id }}</td>
                                    <td>{{ !empty($financeHo->name) ? $financeHo->name : "-" }}</td>
                                    <td>{{ !empty($financeHo->vendor_code) ? $financeHo->vendor_code : "-" }}</td>
                                    <td>{{ !empty($financeHo->city) ? $financeHo->city->name : "-" }}</td>
                                    <td>{{ !empty($financeHo->gst_number) ? $financeHo->gst_number : "-" }}</td>
                                    <td>{{ !empty($financeHo->contact_person) ? $financeHo->contact_person : "-" }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group">
                                            <a href="{{ route('ho.edit', $financeHo->id) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Edit') }}"><i class="fa fa-pencil"></i></a>
                                            <a href="javascript:void(0);" data-toggle="tooltip" title="" class="btn btn-danger remove-button" data-original-title="{{ __('Remove') }}" data-id="{{ $financeHo->id }}"><i class="fa fa-times"></i></a>
                                            <form id="remove-form-{{ $financeHo->id }}" action="{{ route('ho.destroy', $financeHo->id) }}" method="POST" class="d-none">
                                                @csrf
                                                {{ method_field('DELETE') }}
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">
                                    <mark>{{ __('No record found!') }}</mark>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{ $financeHos->withQueryString()->links('pagination.default') }}
            </div>
        </p>
    </div>
@endsection

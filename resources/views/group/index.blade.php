@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Groups') }}
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
                    <form action="{{ route('group.index') }}" method="GET">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <input type="text" name="name" class="form-control" autofocus placeholder="{{ __('Group Name') }}" value="{{ request('name') }}" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <fieldset>
                                        <div class="form-group">
                                            <select class="form-control" name="status">
                                                <option value="">{{ __('-- Select Status --') }}</option>

                                                @if (!empty($modal->statuses))
                                                    @foreach ($modal->statuses as $key => $status)
                                                        <option value="{{ $key }}" {{ (request('status', '-1') == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-xs-2">
                                    <a href="{{ route('group.index', ['page' => request('page')]) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Clear') }}"><i class="fa fa-close"></i></a>
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
                    <a href="{{ route('group.create') }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Add New') }}"><i class="fa fa-plus"></i> {{ __('Add New') }}</a>
                </label>
            </h2>
        </div>

        <p>
            <div class="table-responsive">
                <table class="table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>{{ __('Group Name') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($groups) && !$groups->isEmpty())
                            @foreach ($groups as $group)
                                <tr>
                                    <td class="text-center">{{ $group->id }}</td>
                                    <td>{{ !empty($group->name) ? $group->name : "-" }}</td>
                                    <td>
                                        <label class="switch switch-success">
                                            <input type="checkbox" {{ $group->status == $modal::STATUS_ACTIVE ? 'checked=""' : '' }} disabled="disabled" />
                                            <span data-toggle="tooltip" title="" data-original-title="{{ $modal->statuses[$group->status] }}"></span>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group">
                                            <a href="{{ route('group.edit', $group->id) }}" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="{{ __('Edit') }}"><i class="fa fa-pencil"></i></a>
                                            <a href="javascript:void(0);" data-toggle="tooltip" title="" class="btn btn-danger remove-button" data-original-title="{{ __('Remove') }}" data-id="{{ $group->id }}"><i class="fa fa-times"></i></a>
                                            <form id="remove-form-{{ $group->id }}" action="{{ route('group.destroy', $group->id) }}" method="POST" class="d-none">
                                                @csrf
                                                {{ method_field('DELETE') }}
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center">
                                    <mark>{{ __('No record found!') }}</mark>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{ $groups->withQueryString()->links('pagination.default') }}
            </div>
        </p>
    </div>
@endsection

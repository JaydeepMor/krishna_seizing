@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Create New Group') }}
            </h1>
        </div>
    </div>
    <!-- END Page Header -->

    <div class="block">
        <div class="row">
            <div class="col-lg-12">
                <div class="block">
                    <div class="block-title">
                        <h2><strong class="color-red">*</strong> {{ __('is required field') }}</h2>
                    </div>
                    <form action="{{ route('group.store') }}" method="post">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>* {{ __('Group Name') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="name" name="name" class="form-control" autofocus value="{{ old('name') }}" required />
                                        @error('name')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>* {{ __('Status') }}</legend>
                                    <div class="form-group">
                                        <select class="form-control" name="status" required>
                                            <option value="-1">{{ __('-- Select Status --') }}</option>

                                            @if (!empty($modal->statuses))
                                                @foreach ($modal->statuses as $key => $status)
                                                    @php
                                                        $selected = ((old('status', $modal::STATUS_ACTIVE) >= '0') && old('status', $modal::STATUS_ACTIVE) == $key) ? 'selected' : '';
                                                    @endphp

                                                    <option value="{{ $key }}" {{ $selected }}>{{ $status }}</option>
                                                @endforeach
                                            @endif
                                        </select>

                                        @error('status')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Finance Company') }}</legend>
                                    <div class="form-group">
                                        <select name="finance_company_id[]" id="finance_company_id" multiple="multiple" class="form-control multiselect-inline">
                                            @if (!empty($financeCompanies))
                                                @foreach ($financeCompanies as $financeCompany)
                                                    @php
                                                        $selected = (in_array($financeCompany->id, old('finance_company_id', []))) ? 'selected' : '';
                                                    @endphp

                                                    <option value="{{ $financeCompany->id }}" {{ $selected }}>{{ $financeCompany->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-floppy-o"></i> {{ __('Save') }}</button>
                                <button type="reset" class="btn btn-sm btn-warning"><i class="fa fa-repeat"></i> {{ __('Reset') }}</button>
                            </div>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

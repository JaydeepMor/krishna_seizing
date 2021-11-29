@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Create New Finance HO') }}
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
                    <form action="{{ route('ho.store') }}" method="post">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>* {{ __('Name') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="name" name="name" class="form-control" autofocus value="{{ old('name') }}" required="true" />
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
                                    <legend>{{ __('Address') }}</legend>
                                    <div class="form-group">
                                        <textarea name="address" id="address" class="form-control">{{ old('address') }}</textarea>
                                        @error('address')
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
                                    <legend>{{ __('Vendor Code') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="vendor_code" name="vendor_code" class="form-control" value="{{ old('vendor_code') }}" />
                                        @error('vendor_code')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('GST Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="gst_number" name="gst_number" class="form-control" value="{{ old('gst_number') }}" />
                                        @error('gst_number')
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
                                    <legend>{{ __('Contact Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="contact_number" name="contact_number" class="form-control" value="{{ old('contact_number') }}" />
                                        @error('contact_number')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Contact Person') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="contact_person" name="contact_person" class="form-control" value="{{ old('contact_person') }}" />
                                        @error('contact_person')
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
                                    <legend>{{ __('Email') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="email" name="email" class="form-control" value="{{ old('email') }}" />
                                        @error('email')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('City') }}</legend>
                                    <div class="form-group">
                                        <select name="city_id" id="city_id" class="form-control">
                                            <option value="">{{ __('-- Select City --') }}</option>

                                            @if (!empty($cities) && !$cities->isEmpty())
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}" {{ ($city->id == old('city_id')) ? "selected" : "" }}>{{ $city->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('city_id')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
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

@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Edit Vehicle') }}
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
                    <form action="{{ route('vehicle.update', $row->id) }}" method="POST">
                        @csrf
                        {{ method_field('PUT') }}

                        <div class="row">
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Loan Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="loan_number" name="loan_number" class="form-control" autofocus value="{{ old('loan_number', $row->loan_number) }}" />
                                        @error('loan_number')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Customer Name') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ old('customer_name', $row->customer_name) }}" />
                                        @error('customer_name')
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
                                    <legend>{{ __('Model') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="model" name="model" class="form-control" value="{{ old('model', $row->model) }}" />
                                        @error('model')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Registration Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="registration_number" name="registration_number" class="form-control" value="{{ old('registration_number', $row->registration_number) }}" />
                                        @error('registration_number')
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
                                    <legend>{{ __('Chassis Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="chassis_number" name="chassis_number" class="form-control" value="{{ old('chassis_number', $row->chassis_number) }}" />
                                        @error('chassis_number')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Engine Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="engine_number" name="engine_number" class="form-control" value="{{ old('engine_number', $row->engine_number) }}" />
                                        @error('engine_number')
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
                                    <legend>{{ __('ARM RRM') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="arm_rrm" name="arm_rrm" class="form-control" value="{{ old('arm_rrm', $row->arm_rrm) }}" />
                                        @error('arm_rrm')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Mobile Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="mobile_number" name="mobile_number" class="form-control" value="{{ old('mobile_number', $row->mobile_number) }}" />
                                        @error('mobile_number')
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
                                    <legend>{{ __('BRM') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="brm" name="brm" class="form-control" value="{{ old('brm', $row->brm) }}" />
                                        @error('brm')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Final Confirmation') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="final_confirmation" name="final_confirmation" class="form-control" value="{{ old('final_confirmation', $row->final_confirmation) }}" />
                                        @error('final_confirmation')
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
                                    <legend>{{ __('Final Manager Name') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="final_manager_name" name="final_manager_name" class="form-control" value="{{ old('final_manager_name', $row->final_manager_name) }}" />
                                        @error('final_manager_name')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Final Manager Mobile Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="final_manager_mobile_number" name="final_manager_mobile_number" class="form-control" value="{{ old('final_manager_mobile_number', $row->final_manager_mobile_number) }}" />
                                        @error('final_manager_mobile_number')
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
                                    <legend>{{ __('Address') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $row->address) }}" />
                                        @error('address')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Branch') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="branch" name="branch" class="form-control" value="{{ old('branch', $row->branch) }}" />
                                        @error('branch')
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
                                    <legend>{{ __('BKT') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="bkt" name="bkt" class="form-control" value="{{ old('bkt', $row->bkt) }}" />
                                        @error('bkt')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Area') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="area" name="area" class="form-control" value="{{ old('area', $row->area) }}" />
                                        @error('area')
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
                                    <legend>{{ __('Region') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="region" name="region" class="form-control" value="{{ old('region', $row->region) }}" />
                                        @error('region')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Lot Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" class="form-control" value="{{ $row->lot_number }}" disabled />
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-floppy-o"></i> {{ __('Update') }}</button>
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

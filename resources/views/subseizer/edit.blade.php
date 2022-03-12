@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Edit Sub Seizer') }}
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
                    <form action="{{ route('subseizer.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{ method_field('PUT') }}

                        <div class="row">
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>* {{ __('Name') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="name" name="name" class="form-control" autofocus value="{{ old('name', $row->name) }}" required />
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
                                        <textarea class="form-control" id="address" name="address">{{ old('address', $row->address) }}</textarea>
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
                                    <legend>* {{ __('Email') }}</legend>
                                    <div class="form-group">
                                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $row->email) }}" required />
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
                                    <legend>{{ __('Contact Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="contact_number" name="contact_number" class="form-control" value="{{ old('contact_number', $row->contact_number) }}" />
                                        @error('contact_number')
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
                                    <legend>{{ __('Team Leader') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="team_leader" name="team_leader" class="form-control" value="{{ old('team_leader', $row->team_leader) }}" />
                                        @error('team_leader')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>* {{ __('IMEI Number') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="imei_number" name="imei_number" class="form-control" value="{{ old('imei_number', $row->imei_number) }}" required />
                                        @error('imei_number')
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
                                    <legend>{{ __('Status') }}</legend>
                                    <div class="form-group">
                                        <select class="form-control" name="status">
                                            <option value="-1">{{ __('-- Select Status --') }}</option>

                                            @if (!empty($modal->statuses))
                                                @foreach ($modal->statuses as $key => $status)
                                                    @php
                                                        $selected = ((old('status', $row->status) >= '0') && old('status', $row->status) == $key) ? 'selected' : '';
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
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Group') }}</legend>
                                    <div class="form-group">
                                        <select class="form-control" name="group_id" id="group_id">
                                            <option value="">{{ __('-- Select Group --') }}</option>

                                            @if (!empty($groups) && !$groups->isEmpty())
                                                @foreach ($groups as $group)
                                                    <option value="{{ $group->id }}" {{ $group->id == old('group_id', $row->group_id) ? 'selected' : '' }}>{{ $group->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('group_id')
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
                                    <legend>
                                        {{ __('Upload New ID Proof') }}
                                    </legend>
                                    <div class="form-group">
                                        <div class="alert alert-danger fade-in show" role="alert">
                                            * {{ __("Only upload id proof if you want to change.") }}
                                            <br />
                                            * {{ __("Old uploaded id proof remains same if you leave blank.") }}
                                        </div>
                                        <input type="file" name="id_proof" id="id_proof" class="form-control" accept="image/png, image/jpg, image/jpeg" />
                                        @error('id_proof')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>
                                        {{ __('Upload New Selfie') }}
                                    </legend>
                                    <div class="form-group">
                                        <div class="alert alert-danger fade-in show" role="alert">
                                            * {{ __("Only upload selfie if you want to change.") }}
                                            <br />
                                            * {{ __("Old uploaded selfie remains same if you leave blank.") }}
                                        </div>
                                        <input type="file" name="selfie" id="selfie" class="form-control" accept="image/png, image/jpg, image/jpeg" />
                                        @error('selfie')
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
                                    <legend>{{ __('Reference Name') }}</legend>
                                    <div class="form-group">
                                        <input type="text" name="reference_name" id="reference_name" class="form-control" value="{{ old('reference_name', $row->reference_name) }}" />
                                        @error('reference_name')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Reference Mobile Number') }}</legend>
                                    <div class="form-group">
                                        <input type="number" name="reference_mobile_number" id="reference_mobile_number" class="form-control" value="{{ old('reference_mobile_number', $row->reference_mobile_number) }}" />
                                        @error('reference_mobile_number')
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
                                    <legend>{{ __('Password') }}</legend>
                                    <div class="form-group">
                                        <input type="password" id="password" name="password" class="form-control" value="{{ old('password') }}" />
                                        @error('password')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Confirm Password') }}</legend>
                                    <div class="form-group">
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}" />
                                        @error('password_confirmation')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        @if (!empty($vehicleFields))
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend>* {{ __('Vehicle Allowed Fields') }}</legend>
                                        <div class="form-group">
                                            <select name="vehicle_allowed_fields[]" id="vehicle_allowed_fields" multiple="multiple" class="form-control multiselect-inline">
                                                @foreach ($vehicleFields as $vehicleField)
                                                    <option value="{{ $vehicleField }}" {{ in_array($vehicleField, old('vehicle_allowed_fields', $vehicleSelectedFields)) ? 'selected' : '' }}>{{ $vehicleField }}</option>
                                                @endforeach
                                            </select>
                                            @error('vehicle_allowed_fields')
                                                <em class="color-red error invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </em>
                                            @enderror
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        @endif
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

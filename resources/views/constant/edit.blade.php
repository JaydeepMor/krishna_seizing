@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('Edit Constant') }}
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
                    <form action="{{ route('constant.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{ method_field('PUT') }}

                        <div class="row">
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>* {{ __('Key') }}</legend>
                                    <div class="form-group">
                                        <input type="text" id="key" name="key" class="form-control" autofocus value="{{ old('key', $row->key) }}" required="true" readonly="true" />
                                        @error('key')
                                            <em class="color-red error invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </em>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend>{{ __('Value') }}</legend>
                                    <div class="form-group">
                                        @if ($row->key == 'RELEASED_APPLICATION')
                                                <input type="file" name="value" id="value" class="form-control" />
                                            @else
                                                <textarea name="value" id="value" class="form-control">{{ old('value', $row->value) }}</textarea>
                                        @endif
                                        @error('value')
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

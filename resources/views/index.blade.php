@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-pie_chart"></i>{{ __('Dashboard') }}
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

    <!-- Example Block -->
    <div class="block">
        <!-- Example Title -->
        <div class="block-title">
            <h2>{{ __('Counters') }}</h2>
        </div>
        <!-- END Example Title -->

        <!-- Example Content -->
        <p>
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('subseizer.index') }}" class="widget widget-hover-effect1">
                        <div class="widget-simple">
                            <div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
                                <i class="gi gi-user"></i>
                            </div>
                            <h3 class="widget-content text-right animation-pullDown">
                                <strong>{{ $data['users'] }}</strong><br>
                                <small>{{ __('Sub Seizers') }}</small>
                            </h3>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('ho.index') }}" class="widget widget-hover-effect1">
                        <div class="widget-simple">
                            <div class="widget-icon pull-left themed-background-fire animation-fadeIn">
                                <i class="gi gi-header"></i>
                            </div>
                            <h3 class="widget-content text-right animation-pullDown">
                                <strong>{{ $data['finance_hos'] }}</strong><br>
                                <small>{{ __('Finance HOs') }}</small>
                            </h3>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('company.index') }}" class="widget widget-hover-effect1">
                        <div class="widget-simple">
                            <div class="widget-icon pull-left themed-background-spring animation-fadeIn">
                                <i class="gi gi-building"></i>
                            </div>
                            <h3 class="widget-content text-right animation-pullDown">
                                <strong>{{ $data['finance_companies'] }}</strong><br>
                                <small>{{ __('Finance Companies') }}</small>
                            </h3>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('group.index') }}" class="widget widget-hover-effect1">
                        <div class="widget-simple">
                            <div class="widget-icon pull-left themed-background-amethyst animation-fadeIn">
                                <i class="gi gi-building"></i>
                            </div>
                            <h3 class="widget-content text-right animation-pullDown">
                                <strong>{{ $data['groups'] }}</strong><br>
                                <small>{{ __('Groups') }}</small>
                            </h3>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="widget">
                        <div class="widget-extra themed-background-dark">
                            <div class="widget-options">
                                
                            </div>
                            <h3 class="widget-content-light">
                                {{ __('Sub') }} <strong>{{ __('Seizers') }}</strong>
                                <br />
                                <small>{{ __('Activation Expiration In 3 Days') }}</small>
                            </h3>
                        </div>
                        <div class="widget-extra">
                            <table class="table table-vcenter table-striped">
                                <tbody>
                                    <th class="text-center">#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('IMEI Number') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Contact Number') }}</th>
                                    <th>{{ __('Subscription From / To') }}</th>
                                    <th>{{ __('Subscribe') }}</th>
                                </tbody>
                                <tbody>
                                    @if (!empty($data['seizers_activations']) && !$data['seizers_activations']->isEmpty())
                                        @foreach ($data['seizers_activations'] as $subscription)
                                            @php
                                                $user = $subscription->user;

                                                if (empty($user) || $user->status == $user::STATUS_INACTIVE) {
                                                    continue;
                                                }
                                            @endphp

                                            <tr>
                                                <td class="text-center">{{ $user->id }}</td>
                                                <td>{{ !empty($user->name) ? $user->name : "-" }}</td>
                                                <td>{{ !empty($user->imei_number) ? $user->imei_number : "-" }}</td>
                                                <td>{{ !empty($user->address) ? $user->address : "-" }}</td>
                                                <td>{{ !empty($user->contact_number) ? $user->contact_number : "-" }}</td>
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
                        </div>
                    </div>
                </div>
            </div>
        </p>
        <!-- END Example Content -->
    </div>
    <!-- END Example Block -->
@endsection

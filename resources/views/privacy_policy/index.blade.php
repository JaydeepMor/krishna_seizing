@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="content-header">
        <div class="header-section">
            <h1>
                <i class="gi gi-table"></i>{{ __('PRIVACY POLICY') }}
            </h1>
        </div>
    </div>
    <!-- END Page Header -->

    <div class="block">
        <p>
            <h4>We respect your privacy. On this page, we explain what kinds of personal information we collect, why, and we how we can and will protect your privacy.</h4>

            <br />

            <h3 class="sub-header"><b>Why we collect personal information</b></h3>

            <h4><b>Products and services</b></h4>
            <h4>
                We need some personal details to process your order. Who are you, how can we contact you? We won't ask more than absolutely required. The data we ask various per order, as we need other information for software licenses than we do for physical products.
            </h4>

            <br />

            <h4><b>How we store your data</b></h4>
            <h4>
                Everything you send to us, will be transported over an SSL encrypted connection. This means that it is impossible for hackers to tap the information. Second, we store your information in our database that's only accessible by us and relevant parties that develop our website.
            </h4>

            <br />

            <h4><b>Who will see your data</b></h4>
            <h4>
                Nobody! Only {{ env('APP_NAME') }} employees who need access to certain kinds of personal information to process vehicles or serve any other need will have access to this information. We will never share your personal details with anybody else.
            </h4>

            <br />

            <h3 class="sub-header text-center"><b>Contact Us</b></h3>
            <h4 class="text-center">
                You can always contact us if you have any questions regarding your privacy or the data we've collected about you.
            </h4>

            <br />

            <div class="row">
                <div class="col-sm-3">
                    &nbsp;
                </div>
                <div class="col-sm-3">
                    <a href="mailto:{{ env('ADMIN_EMAIL', 'it.jaydeep.mor@gmail.com') }}" class="widget widget-hover-effect1">
                        <div class="widget-simple">
                            <div class="widget-icon pull-left themed-background-fire animation-fadeIn">
                                <i class="gi gi-envelope"></i>
                            </div>
                            <h3 class="widget-content text-right animation-pullDown">
                                {{ env('ADMIN_EMAIL', 'it.jaydeep.mor@gmail.com') }}
                            </h3>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="tel:{{ env('AGENCY_CONTACT', '+91 9106393346') }}" class="widget widget-hover-effect1">
                        <div class="widget-simple">
                            <div class="widget-icon pull-left themed-background-amethyst animation-fadeIn">
                                <i class="gi gi-iphone"></i>
                            </div>
                            <h3 class="widget-content text-right animation-pullDown">
                                {{ env('AGENCY_CONTACT', '+91 9106393346') }}
                            </h3>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3">
                    &nbsp;
                </div>
            </div>
        </p>
    </div>
@endsection

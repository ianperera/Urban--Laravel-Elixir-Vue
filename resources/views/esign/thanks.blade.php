@extends('esign._layout')

@section('title')
    Thank you!
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                <img src="{{ asset('images/usc_logo_v2.png') }}" class="img-responsive center-block">
            </div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <h3>Thank you! You order was electronically signed.</h3>
                <p> Thanks for purchasing one of our buildings! <br>
                    After your dealer collects payment the order will be submitted into our system for delivery schedule. <br>
                    Don't hesitate to let your dealer know if you have any questions or concerns!
                </p>
                <p>
                    &mdash; Urban Shed Concepts
                </p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
@endsection

@section('styles')
    @parent

    <style>
        .container {
            padding-top: 2em;
        }
    </style>
@endsection

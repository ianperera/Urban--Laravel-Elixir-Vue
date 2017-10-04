@extends('app')

@section('main-content')
    <!-- Dashboard -->
    <div class="row">
        <router-view :title="'{{ $params['subtitle'] }}'"></router-view>
    </div>
@endsection

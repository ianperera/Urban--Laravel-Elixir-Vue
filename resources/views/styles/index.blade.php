@extends('app')

@section('main-content')
    <div class="row">
        <router-view :title="'{{ $params['subtitle'] }}'"></router-view>
    </div>
@endsection

@extends('partials._base')

@section('panel-body')
    @include('partials._forms')
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-xs-12 col-md-6">
                {!! Form::label('first_name', 'First Name', ['class' => 'control-label']) !!}
                {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => 'First Name', 'autofocus' => 'true']) !!}
            </div>
            <div class="col-xs-12 col-md-6">
                {!! Form::label('last_name', 'Last Name', ['class' => 'control-label']) !!}
                {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'Last Name']) !!}
            </div>
            <div class="col-xs-12 col-md-6">
                {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}
            </div>
            <div class="col-xs-12 col-md-6">
                {!! Form::label('phone_number', 'Phone', ['class' => 'control-label']) !!}
                {!! Form::text('phone_number', null, ['class' => 'form-control', 'placeholder' => 'Phone']) !!}
            </div>
            <div class="col-xs-12 col-md-4">
                {!! Form::label('address', 'Address', ['class' => 'control-label']) !!}
                {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => 'Address']) !!}
            </div>
            <div class="col-xs-12 col-md-3">
                {!! Form::label('city', 'City', ['class' => 'control-label']) !!}
                {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => 'City']) !!}
            </div>
            <div class="col-xs-12 col-md-3">
                {!! Form::label('state', 'State', ['class' => 'control-label']) !!}
                {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => 'State']) !!}
            </div>
            <div class="col-xs-12 col-md-2">
                {!! Form::label('zip', 'Zip', ['class' => 'control-label']) !!}
                {!! Form::text('zip', null, ['class' => 'form-control', 'placeholder' => 'Zip']) !!}
            </div>
        </div>
    </div>
@endsection


@section('panel-footer')
    <div class="panel-footer clearfix">
        <div class="pull-right">
            <a href="{{ url($params['route']) }}" class="btn btn-default">Cancel</a>
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection

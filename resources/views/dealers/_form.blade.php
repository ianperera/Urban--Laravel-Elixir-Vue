@extends('partials._base')

@section('panel-body')
    @include('partials._forms')
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-xs-12 col-md-12">
                {!! Form::label('business_name', 'Business Name', ['class' => 'control-label']) !!}
                {!! Form::text('business_name', null, ['class' => 'form-control', 'placeholder' => 'Business Name', 'autofocus' => 'true']) !!}
            </div>
            <div class="col-xs-12 col-md-3">
                {!! Form::label('phone', 'Phone', ['class' => 'control-label']) !!}
                {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Phone']) !!}
            </div>
            <div class="col-xs-12 col-md-3">
                {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}
            </div>
            <div class="col-xs-12 col-md-3">
                {!! Form::label('tax_rate', 'Tax Rate', ['class' => 'control-label']) !!}
                {!! Form::text('tax_rate', null, ['class' => 'form-control', 'placeholder' => 'Tax Rate (%)']) !!}
            </div>
            <div class="col-xs-12 col-md-3">
                {!! Form::label('cash_sale_deposit_rate', 'Deposit % for Cash Sales', ['class' => 'control-label']) !!}
                {!! Form::text('cash_sale_deposit_rate', null, ['class' => 'form-control', 'placeholder' => 'Deposit % for Cash Sales']) !!}
            </div>
            <div class="col-xs-12 col-md-4">
                {!! Form::label('location_id', 'Location', ['class' => 'control-label']) !!}
                {!! Form::select('location_id', $params['data']['locations'], null, ['id' => 'location_id', 'name' => 'location_id', 'data-plugin-selectTwo', 'class' => 'form-control populate']) !!}
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

@extends('partials._base')

@section('panel-body')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <section class="panel">
                    <header class="panel-heading">
                        <h4 class="panel-title">Details</h4>
                    </header>
                    <div class="panel-body">
                        <dl class="dl-horizontal">
                            <dt>Bill #</dt>
                            <dd>{{ $params['data']['item']->number }}</dd>
                            <dt>Contractor</dt>
                            <dd>{{ $params['data']['item']->user->full_name }}</dd>
                            <dt>Date</dt>
                            <dd>{{ $params['data']['item']->date }}</dd>
                            <dt>Mail payment to</dt>
                            <dd>{{ $params['data']['item']->user->email }}</dd>
                        </dl>
                    </div>
                </section>

                @if ($params['data']['item']->billable)
                    <div class="dataTables_wrapper no-footer">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-condensed mb-none no-footer" >
                                <thead>
                                <tr>
                                    <th class="text-center">Building ID</th>
                                    <th class="text-left">Detail</th>
                                    <th class="text-left">Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($params['data']['item']->billable->sortBy('created_at') as $billable_item)

                                    <tr>
                                        <td class="text-center">
                                            <a href="{{ route('buildings.show', ['building' => $billable_item->building->id]) }}">{{ $billable_item->building->serial_number }}</a>
                                        </td>
                                        <td class="text-left">
                                            @if( $billable_item->pivot->expense_type == 'location' )
                                                Delivered to <u>{{ $billable_item->location->name }}</u>
                                            @elseif( $billable_item->pivot->expense_type == 'status' )
                                                {{ $billable_item->building_status->name }}
                                            @endif
                                        </td>
                                        <td class="text-left">${{ number_format($billable_item->pivot->cost, 2) }} </td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-left"><strong>Total</strong></td>
                                </tr>

                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-left">${{ number_format($params['data']['item']->amount, 2) }}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <p>No options found for this building.</p>
                @endif


            </div>
        </div>

    </div>
@endsection

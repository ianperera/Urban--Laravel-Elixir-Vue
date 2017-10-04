@extends('forms.quote-layout')

@section('pdf-content')
    <!-- Header -->
    <table width="800">
        <tr>
            <td width="100%" valign="top" style="text-align: center">
                <img src="{{ url('images/usc_logo_v2.png') }}" alt="USC Logo" style="width: 20%; float: none; clear: both" />
                <div style="margin: 0 auto; font-size: 10px !important; text-align: center; font-weight: bold">
                    urbanshedconcepts.com<br>
                    {{ $dealer->location->name }}<br>
                    {{ $dealer->location->address }},
                    {{ $dealer->location->city }},
                    {{ $dealer->location->state }},
                    {{ $dealer->location->zip }}
                    <br>
                    {{ $dealer->email  }}<br>
                    {{ $dealer->phone  }}
                </div>
            </td>
        <tr>
    </table>

    <br/>

    <?php $buildingOptions = $building->building_options ?? collect();
    if(!$buildingOptions->isEmpty()) {
        $totalMaterials = 0;

        $materials['roof'] = $buildingOptions->last(function($bo) {
            return $bo->category->group === 'roof';
        });
        $materials['trim'] = $buildingOptions->last(function($bo) {
            return $bo->category->group === 'trim';
        });
        $materials['siding'] = $buildingOptions->last(function($bo) {
            return $bo->category->group === 'siding';
        });

        foreach ($materials as $material) {
            $totalMaterials += $material->total_price;

            $key = $buildingOptions->search(function($bo, $key) use ($material) {
                return ($material->option_id === $bo->option_id);
            });

            $buildingOptions->forget($key);
        }
    }

    if(!empty($building->total_options)) {
        $totalOptions = $building->total_options;
        if (!empty($totalMaterials)) {
            $totalOptions -= $totalMaterials;
        }
    }
    ?>

    <!-- Building Information -->
    <table width="800" class="border-cells small-text">
        <tr>
            <td colspan="6" valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">
                <strong>Building Information</strong>
            </td>
        </tr>
        <tr>
            <td valign="center" align="right" width="14%" style="font-weight: bold">Building Package:</td>
            <td valign="center" align="left" width="28%" style="border: 1px solid black;">
                @if ($building->building_package)
                    {{ $building->building_package->name }}
                @endif
            </td>
            <td width="2%"></td>
            <td valign="center" align="center" style="font-weight: bold; border: 1px solid black; background: #dbe7ff">Building Material</td>
            <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">Color</td>
            <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">Price</td>
        </tr>
        <tr>
            <td valign="center" align="right" width="14%">Style & Serial #:</td>
            <td valign="center" align="left" width="28%" class="building-serial-container">

                <table class="building-serial" cellspacing="0">
                    <tr>
                        <td style="border-right: 1px solid black; white-space: nowrap">{{ $building->serial_short_code }}-{{ $building->serial_sizes }}</td>
                        <td>
                            @if($building->serial_ident)
                                {{ $building->serial_ident }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                    </tr>
                </table>

            </td>
            <td></td>
            <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['siding']->option->name ?? ''}}</td>
            <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['siding']->color->name ?? ''}}</td>
            <td valign="center" align="center" style="border: 1px solid black;">@if(isset($materials['siding'])) ${{ number_format($materials['siding']->total_price, 2) }} @endif</td>
        </tr>
        <tr>
            <td valign="center" align="right" width="14%">Size:</td>
            <td valign="center" align="left" width="28%" style="border: 1px solid black;">
                @if( isset($building->width) && isset($building->length) )
                    {{ $building->width }}' x {{ $building->length }}'
                @endif
            </td>
            <td></td>
            <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['trim']->option->name ?? ''}}</td>
            <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['trim']->color->name ?? ''}}</td>
            <td valign="center" align="center" style="border: 1px solid black;">@if(isset($materials['trim'])) ${{ number_format($materials['trim']->total_price, 2) }} @endif</td>
        </tr>
        <tr>
            <td valign="center" align="right" width="14%">Shell Price:</td>
            <td valign="center" align="left" width="28%" style="border: 1px solid black;">
                @if (isset($building->shell_price))
                    ${{ number_format($building->shell_price, 2) }}
                @endif
            </td>
            <td></td>
            <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['roof']->option->name ?? ''}}</td>
            <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['roof']->color->name ?? ''}}</td>
            <td valign="center" align="center" style="border: 1px solid black;">@if(isset($materials['roof'])) ${{ number_format($materials['roof']->total_price, 2) }} @endif</td>
        </tr>
        <tr>
            <td valign="center" align="right" colspan="5"><strong>Total Materials:</strong></td>
            <td valign="center" align="center" class="border-cell" style="background: #e1ffd4">
                @if(isset($totalMaterials)) ${{ number_format($totalMaterials, 2) }} @endif
            </td>
        </tr>
    </table>

    <table width="800" class="border-cells small-text">
        <tr>
            <td width="65%" valign="top">
                <table width="100%" class="border-cells">
                    <tr style="background: #dbe7ff" class="border-cells">
                        <td valign="center" align="center" width="25%">Quantity</td>
                        <td valign="center" align="center" width="40%">Option</td>
                        <td valign="center" align="center" width="15%">Price</td>
                        <td valign="center" align="center" width="25%">Total</td>
                    </tr>

                    @for( $i = 0; $i<=23; $i++ )
                        <tr class="border-cells">
                            @if( ($option = $buildingOptions->shift()) )
                                <td class="with-borders" valign="center" align="center" width="25%">{{ $option->quantity }}</td>
                                <td class="with-borders" valign="center" align="center" width="40%">{{ $option->option->name }}</td>
                                <td class="with-borders" valign="center" align="center" width="15%">${{ number_format($option->unit_price, 2) }}</td>
                                <td class="with-borders" valign="center" align="center" width="25%">${{ number_format($option->total_price, 2) }}</td>
                            @else
                                <td valign="center" align="center" width="25%">&nbsp;</td>
                                <td valign="center" align="center" width="40%"></td>
                                <td valign="center" align="center" width="15%"></td>
                                <td valign="center" align="center" width="25%"> </td>
                            @endif
                        </tr>
                    @endfor

                    <tr>
                        <td valign="center" align="right" colspan="3"><strong>Total Options:</strong></td>
                        <td valign="center" align="center" width="25%" class="border-cell" style="background: #e1ffd4">
                            @if (isset($totalOptions))
                                ${{ number_format($totalOptions, 2) }}
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td width="1%"></td>
            <td width="34%" valign="top">
                <table width="100%" class="border-cells">
                    <!-- Building Price -->
                    <tr>
                        <td colspan="2" align="center" style="padding: 1em 0 0 0">
                            <div style="display: inherit; font-size: 13px !important; font-weight: bold">
                                Building Price
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="dl-form-title">Shell Price:</td>
                        <td width="70%" class="border-cell text-center">
                            @if (isset($building->shell_price))
                                ${{ number_format($building->shell_price, 2) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" class="dl-form-title">Materials:</td>
                        <td width="50%" class="border-cell text-center">
                            @if (isset($totalMaterials))
                                ${{ number_format($totalMaterials, 2) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="dl-form-title">Options:</td>
                        <td width="70%" class="border-cell text-center">
                            @if (isset($totalOptions))
                                ${{ number_format($totalOptions, 2) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="dl-form-title">Building Total:</td>
                        <td width="70%" class="border-cell text-center">
                            @if (isset($building->total_price))
                                ${{ number_format($building->total_price, 2) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="dl-form-title">Tax:</td>
                        <td width="70%" class="border-cell text-center">
                            {{ $order->txt_sales_tax ?? ''}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <div style="display: inherit; font-size: 9px !important; padding: 0.5em 0 0.5em 0">
                                Purchase using cash, check and credit card or RTO.
                            </div>
                        </td>
                    </tr>

                    <!-- RTO -->
                    <tr>
                        <td colspan="2" align="center" style="padding: 1em 0 0 0;">
                            <div style="display: inherit; font-size: 13px !important; font-weight: bold">
                                RTO Options
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="dl-form-title">Security Deposit:</td>
                        <td width="70%" class="border-cell text-center">{{ $order->txt_security_deposit }}</td>
                    </tr>
                    <?php
                    foreach($rtoTerms as $modelId => $model) :
                    $rtoAdvanceMonthlyRenewalPayment = ($order->rto_amount / (float) $model['rto_factor']);
                    $rtoSalesTax = $rtoAdvanceMonthlyRenewalPayment * ($dealer->tax_rate / 100);
                    $rtoTotalAdvanceMonthlyRenewalPayment = $rtoAdvanceMonthlyRenewalPayment + $rtoSalesTax;
                    ?>
                    <tr>
                        <td width="30%" class="dl-form-title"><small>{{$model['value']}} Month:</small></td>
                        <td width="70%" class="border-cell text-center">${{ number_format($rtoTotalAdvanceMonthlyRenewalPayment, 2) }}</td>
                    </tr>
                    <?php endforeach; ?>
                    @if(isset($params['no_tax']))
                        <tr>
                            <td colspan="2" align="center">
                                <div style="display: inherit; font-size: 9px !important;">
                                    (taxes not included)
                                </div>
                            </td>
                        </tr>
                    @endif

                <!-- DELIVERY -->
                    <tr>
                        <td colspan="2" align="center" style="padding: 2em 0 0 0;">
                            <div style="display: inherit; font-size: 13px !important; font-weight: bold">
                                Delivery
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <div style="display: inherit; font-size: 9px !important; padding: 0em 0 1em 0">
                                Free delivery and setup within 30 miles of your local dealership.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="dl-form-title">Est. Mileage:</td>
                        <td width="70%" class="border-cell text-center"></td>
                    </tr>
                    <tr>
                        <td width="30%" class="dl-form-title">Delivery Charge:</td>
                        <td width="70%" class="border-cell text-center"></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <div style="display: inherit; font-size: 9px !important; padding: 2em 0 0.5em 0">
                                *Prices subject to change. All quotes are valid for 7 days from date of quote.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


@endsection
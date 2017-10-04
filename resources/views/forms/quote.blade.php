@extends('forms.quote-layout')

@section('pdf-content')
    <?php

    $buildingLocation['address'] = $orderReference->building_location_address ?? '';
    $buildingLocation['city'] = $orderReference->building_location_city ?? '';
    $buildingLocation['state'] = $orderReference->building_location_state ?? '';
    $buildingLocation['zip'] = $orderReference->building_location_zip ?? '';
    if ($orderReference->building_in_same_address === true) {
        $buildingLocation['address'] = $orderReference->address ?? '';
        $buildingLocation['city'] = $orderReference->city ?? '';
        $buildingLocation['state'] = $orderReference->state ?? '';
        $buildingLocation['zip'] = $orderReference->zip ?? '';
    }

    ?>

<!-- Header -->
<table width="800" class="small-text">
    <tr>
        <td width="49%" valign="top">
            <table width="100%">
                <tr>
                    <td width="35%">Dealer:</td>
                    <td width="65%" style="border-bottom: 1px solid #000;">{{ $dealer->business_name ?? '' }}</td>
                </tr>
                <tr>
                    <td width="35%">Sales Person:</td>
                    <td width="65%" style="border-bottom: 1px solid #000;">{{ $order->sales_person ?? '' }}</td>
                </tr>
                <tr>
                    <td width="35%">Order Date:</td>
                    <td width="65%" style="border-bottom: 1px solid #000;">{{ $order->order_date ?? '' }}</td>
                </tr>
                <tr>
                    <td width="35%">Est. Delivery Period:</td>
                    <td width="65%" style="border-bottom: 1px solid #000;">
                        @if($order->ced_start && $order->ced_end)
                            {{ $order->ced_start }} - {{ $order->ced_end }}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
        <td width="1%"></td>
        <td width="49%" valign="top" style="font-size: 16px; text-align: center;">
            <table width="100%">
                <tr>
                    <td valign="top" width="40%" style="font-size: 9px;">
                        <table width="40%" style="float: left;">
                            <tr><td><strong>Tax Rate</strong></td></tr>
                            <tr>
                                <td>
                                    @if($dealer->tax_rate)
                                        {{ $dealer->tax_rate }}%
                                    @endif
                                </td>
                            </tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                        </table>
                        <table width="60%">
                            <tr><td><strong>Order Type</strong></td></tr>
                            @if ($order->sale_type)
                                <tr><td><i class="fa {{ ( $order->sale_type == 'dealer-inventory' ) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Inventory</td></tr>
                                <tr><td><i class="fa {{ ( $order->sale_type == 'custom-order' ) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Custom</td></tr>
                                <tr><td><i class="fa {{ ( $order->sale_type == 're-sale' ) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Re-sale</td></tr>
                            @else
                                <tr><td><i class="fa fa-square-o"></i> Inventory</td></tr>
                                <tr><td><i class="fa fa-square-o"></i> Custom</td></tr>
                                <tr><td><i class="fa fa-square-o"></i> Re-sale</td></tr>
                            @endif
                        </table>

                    </td>
                    <td valign="top" width="60%">
                        <img src="{{ url('images/usc_logo_v2.png') }}" alt="USC Logo" style="width: 68px; float: left" />
                        <div style="display: inherit; font-size: 10px !important; height: 68px; vertical-align: middle; ">
                            urbanshedconcepts.com<br>
                            {{ $dealer->email ?? 'info@urbanshedconcepts.com' }}<br>
                            {{ $dealer->phone ?? '602.320.8350' }}
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table width="100%">
                <tr>
                    <td width="17.15%"></td>
                    <td width="42.85%">*Special orders may extend beyond the estimated delivery period</td>
                    <td width="25%" align="right">Order #:</td>
                    <td width="15%">{{ $building->order_id ?? '' }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Customer Information -->
<table width="800" class="small-text border-cells">
    <tr>
        <td colspan="6" valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">
            <strong>Customer Information</strong>
        </td>
    </tr>
    <tr>
        <td valign="center" align="right" width="14%">Name:</td>
        <td valign="center" align="left" width="20%" style="border: 1px solid black;">{{ $orderReference->first_name ?? '' }} {{ $orderReference->last_name ?? '' }}</td>
        <td valign="center" align="right" width="10%">Cell Phone:</td>
        <td valign="center" align="left" width="23%" style="border: 1px solid black;">{{ $orderReference->phone_number ?? '' }}</td>
        <td align="center" colspan="2" style="font-weight: bold">Building Location</td>
    </tr>
    <tr>
        <td valign="center" align="right" width="14%">Address:</td>
        <td valign="center" align="left" width="20%" style="border: 1px solid black;">{{ $orderReference->address ?? '' }}</td>
        <td valign="center" align="right" width="10%">Home Phone:</td>
        <td valign="center" align="left" width="23%" style="border: 1px solid black;">{{ $orderReference->home_phone_number ?? '' }}</td>

        <td valign="center" align="right" width="12%">Address:</td>
        <td valign="center" align="left" width="21%" style="border: 1px solid black;">{{ $buildingLocation['address'] or '' }}</td>
    </tr>
    <tr>
        <td valign="center" align="right" width="14%">City / State / Zip:</td>
        <td valign="center" align="left" width="20%" style="border: 1px solid black;">
            @if($orderReference->city)
            {{ $orderReference->city }} /
            @endif

            @if($orderReference->state)
                {{ $orderReference->state }} /
            @endif

            @if($orderReference->zip)
                {{ $orderReference->zip }} /
            @endif
        </td>
        <td valign="center" align="right" width="10%">Email:</td>
        <td valign="center" align="left" width="23%" style="border: 1px solid black;">{{ $orderReference->email ?? '' }}</td>

        <td valign="center" align="right" width="12%">City / State / Zip:</td>
        <td valign="center" align="left" width="21%" style="border: 1px solid black;">
            @if(isset($buildingLocation['city']) && !empty($buildingLocation['city']))
                {{ $buildingLocation['city'] }} /
            @endif

            @if(isset($buildingLocation['state']) && !empty($buildingLocation['state']))
                {{ $buildingLocation['state'] }} /
            @endif

            @if(isset($buildingLocation['zip']) && !empty($buildingLocation['zip']) )
                {{ $buildingLocation['zip'] }} /
            @endif
        </td>
    </tr>
</table>

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

            $key = $buildingOptions->search(function($bo) use ($material) {
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
        <td colspan="7" valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">
            <strong>Building Information</strong>
        </td>
    </tr>
    <tr>
        <td valign="center" align="right" width="14%" style="font-weight: bold">Building Package:</td>
        <td valign="center" align="left" width="28%" style="border: 1px solid black;">
            {{ $building->building_package->name ?? ''}}
        </td>
        <td width="2%"></td>
        <td valign="center" align="center" style="font-weight: bold; border: 1px solid black; background: #dbe7ff" colspan="2">Building Material</td>
        <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">Color</td>
        <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">Price</td>
    </tr>
    <tr>
        <td valign="center" align="right" width="14%">Style & Serial #:</td>
        <td valign="center" align="left" width="28%" class="building-serial-container">

            @if ($order->sale_type)
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
            @endif

        </td>
        <td></td>
        <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff; width: 10%;">Siding</td>
        <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['siding']->option->name or ''}}</td>
        <td valign="center" align="center" style="border: 1px solid black; width: 17%;">{{ $materials['siding']->color->name or ''}}</td>
        <td valign="center" align="center" style="border: 1px solid black; width: 10%;">@if(isset($materials['siding'])) ${{ number_format($materials['siding']->total_price, 2) }} @endif</td>
    </tr>
    <tr>
        <td valign="center" align="right" width="14%">Size:</td>
        <td valign="center" align="left" width="28%" style="border: 1px solid black;">
            @if( isset($building->width) && isset($building->length) )
                {{ $building->width }}' x {{ $building->length }}'
            @endif
        </td>
        <td></td>
        <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">Trim</td>
        <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['trim']->option->name or ''}}</td>
        <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['trim']->color->name or ''}}</td>
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
        <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">Roof</td>
        <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['roof']->option->name or ''}}</td>
        <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['roof']->color->name or ''}}</td>
        <td valign="center" align="center" style="border: 1px solid black;">@if(isset($materials['roof'])) ${{ number_format($materials['roof']->total_price, 2) }} @endif</td>
    </tr>
    <tr>
        <td valign="center" align="right" colspan="6"><strong>Total Materials:</strong></td>
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
                        @if ($building->shell_price)
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
                        @if ($building->total_price)
                            ${{ number_format($building->total_price, 2) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="30%" class="dl-form-title">Tax:</td>
                    <td width="70%" class="border-cell text-center">
                        @if ($order->payment_type == 'rto')
                            RTO
                        @else
                            {{ $order->txt_sales_tax ?? ''}}
                        @endif
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
                    <td width="70%" class="border-cell text-center">
                        {{ $order->txt_security_deposit }}
                    </td>
                </tr>
                <?php
                foreach(\App\Models\Order::$rtoTerms as $modelId => $model) :
                $rtoAdvanceMonthlyRenewalPayment = ($order->rto_amount / (float) $model['rto_factor']);
                $rtoSalesTax = $rtoAdvanceMonthlyRenewalPayment * ($dealer->tax_rate / 100);
                $rtoTotalAdvanceMonthlyRenewalPayment = $rtoAdvanceMonthlyRenewalPayment + $rtoSalesTax;
                ?>
                <tr>
                    <td width="30%" class="dl-form-title"><small>{{$model['value']}} Month:</small></td>
                    <td width="70%" class="border-cell text-center">${{ number_format($rtoTotalAdvanceMonthlyRenewalPayment, 2) }}</td>
                </tr>
                <?php endforeach; ?>

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
                    <td width="70%" class="border-cell text-center">{{ $order->txt_delivery_charge }}</td>
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

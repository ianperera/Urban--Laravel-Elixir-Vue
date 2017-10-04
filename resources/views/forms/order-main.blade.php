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
            <td valign="center" align="left" width="21%" style="border: 1px solid black;">{{ $buildingLocation['address'] ?? '' }}</td>
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

    <!-- Purchase Information -->
    <table width="800" class="border-cells small-text">
        <tr>
            <td colspan="4" valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">
                <strong>Purchase Information</strong>
            </td>
        </tr>
        <tr>
            <td valign="center" align="right" width="14%">Purchase Method:</td>
            <td valign="center" align="center" width="28%" style="border: 1px solid black;">
                <i class="fa {{ ($order->payment_type == 'cash' ) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Outright
                &nbsp;&nbsp;&nbsp;
                <i class="fa {{ ($order->payment_type == 'rto' ) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> RTO
            </td>
            <td valign="center" align="right" width="10%">RTO Option:</td>
            <td valign="center" align="center" width="48%" style="border: 1px solid black;">
                @if($order->rto_term)
                    <i class="fa fa-fw {{ ( $order->rto_term == 24 ) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> 24 months &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-fw {{ ( $order->rto_term == 36 ) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> 36 months &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-fw {{ ( $order->rto_term == 48 ) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> 48 months &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-fw {{ ( $order->rto_term == 60 ) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> 60 months &nbsp;&nbsp;&nbsp;
                @else
                    <i class="fa fa-fw fa-square-o"></i> 24 months &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-fw fa-square-o"></i> 36 months &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-fw fa-square-o"></i> 48 months &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-fw fa-square-o"></i> 60 months &nbsp;&nbsp;&nbsp;
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
            <td valign="center" align="center" style="font-weight: bold; border: 1px solid black;background: #dbe7ff" colspan="2">Building Material</td>
            <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">Color</td>
            <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">Price</td>
        </tr>
        <tr>
            <td valign="center" align="right" width="14%">Style & *Serial #:</td>
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
            <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['siding']->option->name ?? ''}}</td>
            <td valign="center" align="center" style="border: 1px solid black; width: 17%;">{{ $materials['siding']->color->name ?? ''}}</td>
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
            <td valign="center" align="center" style="border: 1px solid black; background: #dbe7ff">Roof</td>
            <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['roof']->option->name ?? ''}}</td>
            <td valign="center" align="center" style="border: 1px solid black;">{{ $materials['roof']->color->name ?? ''}}</td>
            <td valign="center" align="center" style="border: 1px solid black;">@if(isset($materials['roof'])) ${{ number_format($materials['roof']->total_price, 2) }} @endif</td>
        </tr>
        <tr>
            @if (!$building->serial_ident)
                <td valign="center" align="right"></td>
                <td valign="center" align="left">*Serial # to be generated.</td>
            @endif

            <td valign="center" align="right" colspan="{{ $building->serial_ident ? 6:4 }}"><strong>Total Materials:</strong></td>
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
                            @if( ( $option = $buildingOptions->shift()))
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
                <table width="100%">
                    <tr><td align="center"><strong>Delivery Remarks</strong></td></tr>
                    <tr><td><i class="fa {{ ($order->dr_level_pad == 1) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Level pad</td></tr>
                    <tr><td><i class="fa {{ ($order->dr_soft_when_wet == 1) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Soft when wet</td></tr>
                    <tr><td><i class="fa {{ ($order->dr_width_restrictions == 1) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Width restrictions</td></tr>
                    <tr><td><i class="fa {{ ($order->dr_height_restrictions == 1) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Height restrictions</td></tr>
                    <tr><td><i class="fa {{ ($order->dr_must_cross_neighboring_property &&  $order->dr_must_cross_neighboring_property == 1) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Must cross neighboring property</td></tr>
                    <tr><td><i class="fa {{ ($order->dr_requires_site_visit == 1) ? 'fa-check-square-o' : 'fa-square-o' }}"></i> Requires site visit</td></tr>
                    <tr>
                        <td class="border-cell" align="left" valign="top" style="height: 75px; ">
                            <strong>Notes:</strong><br>
                            {{ $order->dr_notes ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="border-cells" width="100%">
                                <tr>
                                    <td width="50%" class="dl-form-title">Shell Price:</td>
                                    <td width="50%" class="border-cell text-center">
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
                                    <td width="50%" class="dl-form-title">Options:</td>
                                    <td width="50%" class="border-cell text-center">
                                        @if (isset($totalOptions))
                                            ${{ number_format($totalOptions, 2) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" class="dl-form-title"><strong>Building Total:</strong></td>
                                    <td width="50%" class="border-cell text-center">
                                        @if ($building->total_price)
                                            ${{ number_format($building->total_price, 2) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" class="dl-form-title">RTO Amount:</td>
                                    <td width="50%" class="border-cell text-center">
                                        @if ($order->payment_type == 'rto')
                                            {{ $order->txt_rto_amount ?? '' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" class="dl-form-title">Net Buydown:</td>
                                    <td width="50%" class="border-cell text-center">
                                        @if ($order->payment_type == 'rto')
                                            {{ $order->txt_rto_net_buydown ?? '' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" class="dl-form-title">Monthly RTO:</td>
                                    <td width="50%" class="border-cell text-center">
                                        @if ($order->payment_type == 'rto')
                                            {{ $order->txt_rto_total_advance_monthly_renewal_payment ?? '' }}</td>
                                        @endif
                                </tr>
                                <tr>
                                    <td width="50%" class="dl-form-title">Security Deposit:</td>
                                    <td width="50%" class="border-cell text-center">
                                        @if ($order->payment_type == 'rto')
                                            {{ $order->txt_security_deposit }}
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td width="50%" class="dl-form-title">Delivery Charge:</td>
                                    <td width="50%" class="border-cell text-center">{{ $order->txt_delivery_charge ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td width="50%" class="dl-form-title">Tax:</td>
                                    <td width="50%" class="border-cell text-center">
                                        @if ($order->payment_type == 'rto')
                                            RTO
                                        @else
                                            {{ $order->txt_sales_tax ?? ''}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" class="dl-form-title">Total:</td>
                                    <td width="50%" class="border-cell text-center">
                                        @if ($order->payment_type == 'rto')
                                            RTO
                                        @else
                                            {{ $order->txt_total_amount_due ?? '' }}
                                        @endif</td>
                                </tr>
                                <tr>
                                    <td width="50%" class="dl-form-title">Min. Deposit:</td>
                                    <td width="50%" class="border-cell text-center">{{ $order->txt_deposit_amount ?? ''}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="800" class="border-cells">
        <tr>
            <td class="border-cell" colspan="3" style="padding: 0.5em; font-size: 10px; ">
                Payment is due in full upon delivery. There will be a $50 charge for NSF checks. Cancellation after 3 days from order day is subject to a 15% restocking fee. Customer is responsible for all building permits and compliance with local
                regulations. Customer is responsible for ensuring adequate access and a level pad. Deliveries over 30 miles from a dealer are charged a rate of $4/mile.  Setup includes 45 minutes of setup time, additional time is billed at $75/hour.
                Urban Shed Concepts LLC is not responsible for yard or property damage due to lack of access or unfavorable conditions. If the neighboring property must be crossed or entered, the customer is responsbile for obtaining written
                permission prior to delivery. In the event of a incomplete or default payment, Urban Shed Concepts LLC has the right to enter the property without prior notice and repossess the building.
            </td>
        </tr>
        <tr>
            <td colspan="3" height="15px"></td>
        </tr>
        <tr>
            <td width="70%" style="border-bottom: 1px solid black;">
                <br>
                <br>
                <span style="color: #ffffff;">[sig|req|signer1|Customer Signature]</span>
            </td>
            <td width="5%"></td>
            <td width="25%" style="border-bottom: 1px solid black;">
                <br>
                <br>
                <span style="color: #ffffff;">[date|req|signer1|Date]</span>
            </td>
        </tr>
        <tr>
            <td width="70%" valign="top">Customer Signature</td>
            <td width="5%" valign="top"></td>
            <td width="25%" valign="top">Date</td>
        </tr>
    </table>

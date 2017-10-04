<style type="text/css">

    #cash-receipt {
        font-size: 16px;
    }

    #cash-receipt .text-center {
        text-align: center;
    }

    #cash-receipt h1 {
        font-size: 44px;
        margin: 0;
        padding: 0;
    }

    #cash-receipt h2 {
        font-size: 32px;
        margin: 0;
        padding: 0;
    }

    #cash-receipt h3 {
        font-size: 24px;
        margin: 0;
        padding: 0;
    }

    #cash-receipt h4 {
        font-size: 16px;
        margin: 0;
        padding: 0;
    }

</style>

<div id="cash-receipt">
    <h2 class="text-center">Urban Shed Concepts</h2>
    <div class="text-center">
        <img src="{{ url('images/usc_logo_v2.png') }}" alt="USC Logo" style="width: 120px;" />
    </div>
    <h3 class="text-center">Customer Receipt</h3>

    <h4 class="text-center">Customer Information</h4>
    <table width="800">
        <tr>
            <td width="50%" valign="top" colspan="2" style="padding: 5px 0px">Name: {{ $orderReference->first_name ?? '' }} {{ $orderReference->last_name ?? '' }}</td>
        </tr>
        <tr>
            <td width="50%" valign="top" style="padding: 5px 0px">Phone: {{ $orderReference->phone_number ?? '' }}</td>
            <td width="50%" valign="top" style="padding: 5px 0px">Email: {{ $orderReference->email ?? '' }}</td>
        </tr>
        <tr>
            <td width="50%" valign="top" colspan="2" style="padding: 5px 0px">
                Delivery Address:
                {{ $buildingLocation['address'] ?? '' }}
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


    <h4 class="text-center">Order Information</h4>
    <table width="800">
        <tr><td width="50%" valign="top" style="padding: 5px 0px">Date: {{ $order->order_date ?? '' }}</td></tr>
        <tr><td width="50%" valign="top" style="padding: 5px 0px">Order Number: {{ $building->order_id ?? '' }}</td></tr>
        <tr>
            <td width="50%" valign="top" style="padding: 5px 0px">Estimated Delivery Period:
                @if($order->ced_start && $order->ced_end)
                    {{ $order->ced_start }} - {{ $order->ced_end }}
                @endif
            </td>
        </tr>
        <tr>
            <td width="50%" valign="top">
                Building Price:
                @if ($building->total_price)
                    ${{ number_format($building->total_price, 2) }}
                @endif
            </td>
        </tr>
        <tr><td width="50%" valign="top" style="padding: 5px 0px">Taxes: {{ $order->txt_sales_tax ?? ''}}</td></tr>
        <tr><td width="50%" valign="top" style="padding: 5px 0px">Total Amount: {{ $order->txt_total_amount ?? '' }}</td></tr>
        <tr>
            <td width="50%" valign="top" style="padding: 5px 0px">
                Purchase Method:
                @if ($order->payment_type && $order->payment_type == 'cash') Outright @endif
                @if ($order->payment_type && $order->payment_type == 'rto')  RTO @endif
            </td>
        </tr>
        <tr><td width="50%" valign="top" style="padding: 5px 0px">Minimum Deposit: {{ $order->txt_deposit_amount ?? ''}}</td></tr>
        <tr><td width="50%" valign="top" style="padding: 5px 0px">Amount Received: {{ $order->txt_deposit_received ?? '' }}</td></tr>
        <tr><td width="50%" valign="top" style="padding: 5px 0px">
                Payment Method:
                <?php $paymentMethods = [ 'cash' => 'Cash', 'check' => 'Check', 'credit_card' => 'Credit Card' ]?>
                @foreach($paymentMethods as $method => $methodTitle)
                    @if( $order->payment_method && $method == $order->payment_method)
                        <i class="fa fa-check-square-o"></i> {{ $methodTitle }}

                        @if(!is_null($order->transaction_id))
                        (# {{ $order->transaction_id }}) (last 4)
                        @else
                        (#&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) (last 4)
                        @endif
                    @else
                        <i class="fa fa-square-o fa-fw"></i>{{ $methodTitle }}
                    @endif
                @endforeach
            </td></tr>
        <tr><td width="50%" valign="top" style="padding: 5px 0px">Balance Due: {{ $order->txt_balance ?? ''}}</td></tr>
    </table>

    <br>
    <br>
    <p>We appreciate your business!</p>
    <p>
        As a small business, our primary goal is to ensure your complete satisfaction - if there is anything we can do to
        complete this experience, don't hesitate to ask! We also rely heavily on the feedback from our customers, both
        in generating new business and letting us know how we can better serve you.
    </p>

    <h2 class="text-center">Like us on:</h2>
    <h3 class="text-center">www.facebook.com/urbanshedconcepts</h3>
    <h3 class="text-center">www.instagram.com/urbanshedconcept</h3>
    <h3 class="text-center">www.yelp.com/biz/urban-shed-concepts-phoenix</h3>

    <br>
    <p>*Additional fees for delivery and setup may be incurred if the delivery site is not properly prepared for delivery.</p>
    <p>See the Urban Shed Concepts Dispatch and Delivery document for more information.</p>
</div>
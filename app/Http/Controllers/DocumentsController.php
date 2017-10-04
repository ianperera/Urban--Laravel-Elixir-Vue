<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Building;
use App\Models\Order;
use App\Models\OptionCategory;
use App\Models\OrderReference;
use App\Models\Style;
use App\Models\Dealer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PDF;
use Validator;

class DocumentsController extends Controller
{
    public function __construct()
    {
    }

    public function priceList(Request $request) {
        $styles = Style::with('building_models')->active()->get();
        $categories = OptionCategory::with(['options' => function($query) {
            $query->where('is_active', 'yes');
        }])->where('id', '!=', 1)->get();

        $pdf = PDF::loadView('documents.pdf-price-list', compact('styles', 'categories'));
        $pdf->setPaper('Letter');
        return $pdf->stream('USC_price-list.pdf');
    }

    public function orderForm(Request $request) {
        $validator = Validator::make($request->all(), ['id' => 'required|numeric|exists:dealers,id,deleted_at,NULL']);
        if ($validator->fails()) return response($validator->errors()->all());

        $dealer = Dealer::findOrFail($request->input('id'));

        $pdf = PDF::loadView('documents.pdf-order-form', [
            'dealer' => $dealer,
            'order' => new Order,
            'orderReference' => new OrderReference,
            'building' => new Building
        ]);
        $pdf->setPaper('Letter');
        return $pdf->stream('USC_order-form.pdf');
    }

    public function rtoDocs(Request $request) {

        $validator = Validator::make($request->all(), ['rto_type' => 'required|in:buydown,no-buydown']);
        if ($validator->fails()) return response($validator->errors()->all());

        $order = new Order;
        $order->rto_type = $request->input('rto_type');
        $params['no_dates'] = true;

        $pdf = PDF::loadView('documents.pdf-rto-docs', [
            'dealer' => new Dealer,
            'order' => $order,
            'building' => new Building,
            'orderReference' => new OrderReference,
            'params' => $params,
        ]);
        $pdf->setPaper('Letter');
        return $pdf->stream('USC_rto-documents.pdf');
    }

    public function promo99(Request $request) {
        $pdf = PDF::loadView('documents.pdf-rto-promo99');
        $pdf->setPaper('Letter');
        return $pdf->stream('USC_promo99.pdf');
    }

    public function buildingConfiguration(Request $request) {
        $pdf = PDF::loadView('documents.pdf-building-configuration');
        $pdf->setPaper('Letter');
        return $pdf->stream('USC_building-configuration.pdf');
    }

    public function deliveryForm(Request $request) {
        $pdf = PDF::loadView('documents.pdf-delivery-form', [
            'order' => new Order,
            'orderReference' => new OrderReference,
            'building' => new Building
        ]);
        $pdf->setPaper('Letter');
        return $pdf->stream('USC_delivery-form.pdf');
    }

    public function neightborRelease(Request $request) {
        $pdf = PDF::loadView('documents.pdf-neighbor-release');
        $pdf->setPaper('Letter');
        return $pdf->stream('USC_neighbor-release-form.pdf');
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Models\Order;
use App\Models\Sale;
use App\Models\Building;
use App\Models\BuildingStatus;

class HomeController extends Controller
{
	/**
     * Get all options
     * @param IndexBuildingStatusRequest $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return array
     */
    public function getData()
    {
    	$orders = Order::whereIn('status_id', ['signed', 'review_needed', 'signature_pending'])->get();
    	$ordersTotalRetail = 0;
    	foreach ($orders as $order) {
    		$ordersTotalRetail += $order->total_sales_price;
    	}

        $sales = Sale::whereIn('status_id', ['open', 'updated'])->get();
        $salesTotalRetail = 0;
        foreach ($sales as $sale) {
            if ($sale->order) $salesTotalRetail += $sale->order->total_sales_price;
        }

        $lastMonthSales = Sale::where([['status_id', 'invoiced'], ['created_at', '>=', Carbon::now()->subMonth()]])->get();
        $lastMonthSalesTotalRetail = 0;
        foreach ($lastMonthSales as $sale) {
            if ($sale->order) $lastMonthSalesTotalRetail += $sale->order->total_sales_price;
        }

        $readyToDeliver = BuildingStatus::where('name', 'Ready to Deliver')->first();
        $buildingsProduced = Building::whereHas('building_history', function ($query) use($readyToDeliver) {
                                $query->where([['created_at', '>=', Carbon::now()->subMonth()], ['status_id', $readyToDeliver->id]]);
                            })->get();
        $buildingsProducedTotalRetail = 0;
        foreach ($buildingsProduced as $building) {
            if ($sale->order) $buildingsProducedTotalRetail += $building->total_price;
        }

        $buildings = Building::whereHas('last_location', function ($query) {
                                $query->where('created_at', '>=', Carbon::now()->subMonth());
                            })->withCount('locations')->get();
        $buildingsMoved = [];
        foreach ($buildings as $building) {
            if ($building->locations_count > 1) {
                $buildingsMoved[] = $building;
            }
        }
        $buildingsMoved = collect($buildingsMoved);
        $buildingsMovedTotalRetail = 0;
        foreach ($buildingsMoved as $building) {
            if ($sale->order) $buildingsMovedTotalRetail += $building->total_price;
        }

        $quotes = Order::where([['status_id', 'draft'], ['created_at', '>=', Carbon::now()->subMonth()]])->get();
        $quotesTotalRetail = 0;
        foreach ($quotes as $quote) {
            $quotesTotalRetail += $quote->total_sales_price;
        }

    	$data = [
            'date' => Carbon::now()->format('F Y'),
    		'needing_rewiew_orders_count' => $orders->count(),
    		'orders_total_retail' => $ordersTotalRetail,
            'needing_rewiew_sales_count' => $sales->count(),
            'sales_total_retail' => $salesTotalRetail,
            'last_month_invoised_sales_count' => $lastMonthSales->count(),
            'last_month_sales_total_retail' => $lastMonthSalesTotalRetail,
            'building_produced_count' => $buildingsProduced->count(),
            'building_produced_total_retail' => $buildingsProducedTotalRetail,
            'building_moved_count' => $buildingsMoved->count(),
            'building_moved_total_retail' => $buildingsMovedTotalRetail,
            'quotes_count' => $quotes->count(),
            'quotes_total_retail' => $quotesTotalRetail
    	];
    	return response()->json($data);
    }
}

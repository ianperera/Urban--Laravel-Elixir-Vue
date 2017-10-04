<?php

namespace App\Services\Sales;

use App\Models\Sale;
use App\Models\Order;
use App\Services\Orders\OrderService;
use App\Services\Building\BuildingService;
use App\Repositories\SalesRepository;

use DB;
use Uuid;
use PDF;
use Storage;
use Store;
use Helper;

use Carbon\Carbon;

class SaleService
{

    /**
     * @param Order $order
     * @param OrderService $orderService
     * @return Sale
     */
    public function create(Order $order, OrderService $orderService): Sale {

        $sale = null;
        
        DB::transaction(function() use (&$sale, &$order, $orderService)
        {
            $saleParams = [];

            $orderLocation = $orderService->generateLocation($order);
            $saleParams['building_id'] = $order->building_id;
            $saleParams['location_id'] = $orderLocation->id;
            $saleParams['order_id'] = $order->id;
            $saleParams['status_id'] = 'open';
            
            $sale = Sale::create($saleParams);
            $buildingOpts = [
                'update_serial_number' => true,
                'next_status' => true
            ];
            $buildingService = new BuildingService();
            $buildingService->update($order->building, [], $buildingOpts);
        });

        return $sale;
    }
    
    public function cancel($sale) {
        $sale->order()->update([
            'status_id' => 'draft',
            // calc
            /*
            'total_sales_price' => null,
            'deposit_amount' => null,
            'security_deposit' => null,
            'net_buydown' => null,
            'buydown_tax' => null,
            'balance' => null,
            'rto_amount' => null,
            'rto_advance_monthly_renewal_payment' => null,
            'rto_sales_tax' => null,
            'rto_total_advanceMonthly_renewal_payment' => null,
            'rto_factor' => null,
            'sales_tax' => null,
            'total_amount_due' => null,
            'total_amount' => null,
            */

            // reset building
            'building_id' => null, // foreign 
            
        ]);
        $sale->building_id = null;
        $sale->save();
    }

    /** 
    * @param \DateTime $last, \DateTime $now
    */

    public function getCharts( $month, SalesRepository $salesRepo)
    {
        return $salesRepo->getChartData( $month );
    }

    public function getproductionCharts( $month, SalesRepository $salesRepo)
    {
        return $salesRepo->getProductionChartData( $month );
    }
}

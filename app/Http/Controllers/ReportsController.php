<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Sale;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\Reports\BillReportService;
use App\Services\Reports\ExpenseReportService;
use App\Services\Sales\SaleService;
use App\Repositories\BillRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\SalesRepository;

class ReportsController extends Controller
{
    private $route_name = 'reports';

    private $params = [];

    protected $_salesRepository;

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/Reports/', 'page' => 'Reports']
            ],
            'title' => 'Reports',
            'subtitle' => 'Reports',
            'search' => 'yes',
            'filter' => '',
            'items_per_page' => 500,
            'route_name' => $this->route_name,
            'route' => '/' . $this->route_name,
            'data' => null,
        ];
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('spa')->withParams($this->params);
    }
    
    /**
     * Basic method for routing report request per type
     * @param $reportType
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajaxReport($reportType, Request $request) {

        if ( !$request->ajax() )
            return response('', 403);

        switch ($reportType) {
            case 'bills':
                return $this->ajaxBillReport($request, new BillReportService);

            case 'expenses':
                return $this->ajaxExpenseReport($request, new ExpenseReportService);
        }

        return response('Incorrect report type', 404);
    }

    /**
     * Bill report
     * @param Request $request
     * @param BillReportService $reportService
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxBillReport(Request $request, BillReportService $reportService) {

        $response = [];
        $report = $reportService->getReport($request, new BillRepository(new Bill()));

        if ( $report->validator->failed() )
        {
            $response['success'] = false;
            $response['messages']['errors'] = $report->validator->instance()->errors();
        } else
        {
            $response = $report->data;
            $response['success'] = true;
        }

        return response()->json($response);
    }

    /**
     * Expense report
     * @param Request $request
     * @param ExpenseReportService $reportService
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxExpenseReport(Request $request, ExpenseReportService $reportService) {

        $response = [];
        $report = $reportService->getReport($request, new ExpenseRepository(new Expense()));

        if ( $report->validator->failed() )
        {
            $response['success'] = false;
            $response['messages']['errors'] = $report->validator->instance()->errors();
        } else
        {
            $response = $report->data;
            $response['success'] = true;
        }

        return response()->json($response);
    }

    /**
    * @param
    * @return \Illuminate\Http\JsonResponse
    */
    public function chartsData( SaleService $saleService )
    {
        $response = [];
        // $now = new \DateTime();
        // $now = new \dateTime('2016-12-12');
        // $last = (new \DateTime('2016-12-12'))->modify('-1 week');
            $year = date('Y');
            $month = 4;
            $prior = (int)$month - 1;
             $monthDays = $this->_calDays($year,$month);
             if( $this->_calDays($year,$prior) > $this->_calDays($year,$month) ){
                $monthDays = $this->_calDays($year,$prior);
             }   
            
            $data['current'] = $saleService->getCharts($month,new SalesRepository( new Sale() ) );
            $data['monthDays'] = $monthDays;
            $data['prior'] = $saleService->getCharts( $prior,new SalesRepository( new Sale() ) );
        return response()->json(['status' => 200,'data' => $data]);
    }

    protected function _calDays( $year, $month)
    {
        $daysList= [];
        for ($i=1; $i <= (int)date('t',strtotime($year.'-'.$month.'-01')); $i++) { 
                    # code...
                    array_push($daysList, $i);
                }
                return $daysList;
    }

    public function productionChartsData( SaleService $saleService )
    {
        $year = date('Y');
            $month = 4;
            $prior = (int)$month - 1;
             $monthDays = $this->_calDays($year,$month);
             if( $this->_calDays($year,$prior) > $this->_calDays($year,$month) ){
                $monthDays = $this->_calDays($year,$prior);
             }   
            
            $data['current'] = $saleService->getproductionCharts($month,new SalesRepository( new Sale() ) );
            $data['monthDays'] = $monthDays;
            $data['prior'] = $saleService->getproductionCharts( $prior,new SalesRepository( new Sale() ) );
        return response()->json(['status' => 200,'data' => $data]);
    }

}

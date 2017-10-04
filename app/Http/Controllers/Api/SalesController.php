<?php

namespace App\Http\Controllers\Api;

use App\Mail\NewSaleAccepted;
use App\Models\Order;
use Log;
use Auth;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Uuid;
use Store;
use Storage;
use DB;
use Mail;
use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\Building;

use App\Http\Requests;
use App\Http\Requests\IndexSaleRequest;
use App\Http\Requests\SaveSaleRequest;
use App\Http\Requests\Sales\SendEmailRequest;

use App\Http\Controllers\Controller;

use App\Services\Sales\SaleService;
use App\Services\ArrayBuilder\ArrayBuilderAssistant;

class SalesController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexSaleRequest $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return \Illuminate\Http\Response
     */
    public function index(IndexSaleRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Sale());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $result = $abAssistant
            ->apply()
            ->paginate(
                request('page'),
                request('per_page') ? request('per_page') : $this->getPerPageSetting()
            );
        return response()->json($result);
    }

    /**
     * Get resource
     * @param $id
     * @param Request $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return array
     */
    public function show($id, Request $request, ArrayBuilderAssistant $abAssistant) {
        $abAssistant->setModel(new Sale());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $query = $abAssistant->apply()->getQuery();
        try {
            $item = $query->findOrFail($id);
            return response()->json($item);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['Sale is not found.'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SaveOrderRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store($request) 
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SaveSaleRequest|Request $request
     * @param SaleService $saleService
     * @return \Illuminate\Http\Response
     */
    public function update(SaveSaleRequest $request,
                           SaleService $saleService)
    {
        $outputs = [];
        // now only status and invoice
        $inputs = $request->only(['id', 'status_id', 'order_id', 'building_id', 'location_id', 'invoice_id', 'invoice_date']);
        $inputs = array_filter($inputs, 'strlen');
        try {
            $sale = Sale::findOrFail($inputs['id']);
            $sale->update($inputs);

            if ($request->input('note_admin')) {
                Order::where('id', $sale->order_id)->update(['note_admin' => $request->input('note_admin')]);
            }

            if ($inputs['status_id'] === 'cancelled') {
                $building = Building::find($sale->building_id);
                $building->sold_status = null;
                $building->save();
                $saleService->cancel($sale);
            }

            $outputs[] = 'Sale successfully updated.';
            return response()->json($outputs);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

    }
    
    /**
     * Get files specified order
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function statuses(Request $request)
    {
        $statuses = collect(Sale::$statuses);
        return response()->json($statuses);
    }

    /**
     * Send email
     *
     * @param SendEmailRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(SendEmailRequest $request, $id)
    {

        $sale = Sale::with([
            'order.dealer',
            'order.building',
            'order_reference'
        ])->findOrFail($id);

        $email = new NewSaleAccepted($sale);

        // preview
        if ($request->exists('preview')) {
            $email->build();
            return response()->json([
                'to' => $email->to,
                'cc' => $email->cc,
                'subject' => $email->subject,
                'body' => view($email->view, $email->buildViewData())->render()
            ]);
        }

        Mail::send($email);
        if (Mail::failures()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email is not sent.'
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Email successfully submitted.'
        ]);
    }

    public function exportCsv(IndexSaleRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Sale());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $result = $abAssistant->apply()->get()->toArray();

        $header = $this->getExportDataHeader($result[0]);

        Excel::create('export'.date("Ymd-H:i:s"), function($excel) use ($result, $header) {
            $excel->sheet('Sales Export '. date("Y-m-d"), function($sheet) use ($result, $header){
                $sheet->row(1, $header);
                $rowNumber = 2;
                foreach ($result as $key => $item) {
                    $data = $this->getExportDataValues($header, $item);
                    $sheet->row($rowNumber, $data);
                    $rowNumber++;
                }
            });
        })->download('csv');
    }

    public function exportXls(IndexSaleRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Sale());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $result = $abAssistant->apply()->get()->toArray();

        $header = $this->getExportDataHeader($result[0]);

        Excel::create('export'.date("Ymd-H:i:s"), function($excel) use ($result, $header) {
            $excel->sheet('Sales Export '. date("Y-m-d"), function($sheet) use ($result, $header){
                $sheet->row(1, $header);
                $rowNumber = 2;
                foreach ($result as $key => $item) {
                    $data = $this->getExportDataValues($header, $item);
                    $sheet->row($rowNumber, $data);
                    $rowNumber++;
                }
            });
        })->download('xls');
    }

    public function getExportDataHeader($item)
    {
        $header = [];
        if (isset($item['id'])) {
            $header['id'] = 'Sale ID';
        }
        if (isset($item['created_at'])) {
            $header['date_created'] = 'Date Created';
        }
        if (isset($item['order']) && array_key_exists('date_submitted', $item['order'])) {
            $header['date_submitted'] = 'Date Submitted';
        }
        if (isset($item['status'])) {
            $header['status'] = 'Status';
        }
        if (isset($item['order']) && isset($item['order']['order_reference']) && array_key_exists('customer_name', $item['order']['order_reference'])) {
            $header['customer'] = 'Customer';
        }
        if (isset($item['order'])) {
            if (isset($item['order']['order_type'])) {
                $header['order_type'] = 'Order Type';
            }
            if (array_key_exists('payment_type', $item['order'])) {
                $header['payment_type'] = 'Payment Type';
            }
        }
        if (isset($item['invoice_id'])) {
            $header['invoice'] = 'Invoice #';
        }
        if (isset($item['order']['dealer_commission'])) {
            $header['dealer_commission'] = 'Dealer Commission';
        }
        if (isset($item['order']['sales_tax'])) {
            $header['sales_tax'] = 'Sales Tax';
        }
        if (isset($item['building']) && array_key_exists('serial_number', $item['building'])) {
            $header['serial_number'] = 'Building SN';
        }
        if (array_key_exists('dealer', $item['order'])) {
            $header['dealer'] = 'Dealer';
        }
        if (isset($item['retail'])) {
            $header['retail'] = 'Retail';
        }
        if (isset($item['order']['deposit_received'])) {
            $header['deposit_received'] = 'Deposit Amount';
        }
        return $header;
    }

    public function getExportDataValues($header, $item)
    {
        $data = [];
        if (isset($header['id'])) {
            $data[] = $item['id'];
        }
        if (isset($header['date_created'])) {
            $data[] = $item['created_at'];
        }
        if (isset($header['date_submitted'])) {
            $data[] = $item['order']['date_submitted'];
        }
        if (isset($header['status'])) {
            $data[] = $item['status_id'];
        }
        if (isset($header['customer'])) {
            $data[] = $item['order']['order_reference']['customer_name'];
        }
        if (isset($header['order_type'])) {
            $data[] = $item['order']['order_type']['title'];
        }
        if (isset($header['payment_type'])) {
            $data[] = $item['order']['payment_type'];
        }
        if (isset($header['invoice'])) {
            $data[] = $item['invoice_id'];
        }
        if (isset($header['dealer_commission'])) {
            $data[] = $item['order']['dealer_commission'];
        }
        if (isset($header['sales_tax'])) {
            $data[] = $item['order']['sales_tax'];
        }
        if (isset($header['serial_number'])) {
            $data[] = $item['building']['serial_number'];
        }
        if (isset($header['dealer'])) {
            $data[] = $item['order']['dealer']['business_name'];
        }
        if (isset($header['retail'])) {
            $data[] = $item['retail'];
        }
        if (isset($header['deposit_received'])) {
            $data[] = $item['order']['deposit_received'];
        }

        return $data;
    }
}

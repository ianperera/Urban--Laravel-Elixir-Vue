<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BusinessException;
use App\Http\Requests\Orders\DealerOrderStatusRequest;
use App\Models\OrderContact;
use App\Models\Setting;
use App\Models\Building;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Log;
use Auth;
use Exception;
use Uuid;
use Store;
use Storage;
use DB;
use Mail;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\File;

use App\Http\Requests;
use App\Http\Requests\Orders\IndexOrderRequest;
use App\Http\Requests\Orders\ShowOrderRequest;
use App\Http\Requests\Orders\SearchOrdersRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;
use App\Http\Requests\Orders\SaveDealerOrderRequest;
use App\Http\Requests\Orders\GenerateOrderDocumentRequest;
use App\Http\Requests\Orders\GenerateCompleteOrderDocumentRequest;
use App\Http\Requests\Orders\CustomerOrderRequest;

use App\Http\Controllers\Controller;
use App\Services\Orders\Dealer\OrderDealerDocuments;
use App\Services\Orders\Dealer\OrderDealerFormService;
use App\Services\Orders\OrderPdfService;
use App\Services\Orders\OrderService;
use App\Services\Orders\OrderCustomerFormService;

use App\Services\Sales\SaleService;
use App\Services\Files\FileService;
use App\Services\ArrayBuilder\ArrayBuilderAssistant;
use App\Validators\Order\OrderSaleValidator;

class OrdersController extends Controller
{
    /**
     * @var Order
     */
    private $order;

    /**
     * OrdersController constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexOrderRequest     $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return \Illuminate\Http\Response
     */
    public function index(IndexOrderRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Order());
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
     * @param                       $id
     * @param Request               $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return array
     */
    public function show($id, Request $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Order());
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
            return response()->json(['Order is not found.'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SaveOrderRequestRemove $request
     * @return \Illuminate\Http\Response
     */
    public function store($request)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param                            $id
     * @param UpdateOrderRequest|Request $request
     * @param SaleService                $saleService
     * @param OrderService               $orderService
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateOrderRequest $request, SaleService $saleService, OrderService $orderService)
    {
        $outputs = [];

        try {
            $order = Order::where('uuid', $id)->firstOrFail();
            $order->update($request->all());

            if ($request->all()['status_id'] == 'cancelled' || $request->all()['status_id'] == 'submitted') {
                if ($order->sale !== null && $order->sale->status_id == 'invoiced') {
                    $order->sale->status_id = 'updated';
                    $order->sale->save();
                }
            }

            if (request('generate_sale') && !$order->sale) {
                if ($sale = $saleService->create($order, $orderService)) {
                    $outputs[] = "New sale #{$sale->id} successfully created.";
                    $building = Building::find($sale->building_id);
                    $building->sold_status = 'sold';
                    $building->save();
                }
            }

            $outputs[] = 'Order successfully updated.';
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
    public function files(Request $request)
    {
        $orderUuid = $request->route('order_uuid');
        $order = Order::where('uuid', $orderUuid)->first();
        $files = File::where('storable_type', '=', 'order')
            ->where('storable_id', '=', $order->id)->get();

        return response()->json($files);
    }

    /**
     * Get order statuses
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function statuses(Request $request)
    {
        $orderStatuses = collect(Order::$statuses);
        return response()->json($orderStatuses);
    }

    /**
     * Get order types
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function orderTypes(Request $request)
    {
        $orderTypes = collect(Order::$orderTypes);
        return response()->json($orderTypes);
    }

    /**
     * Get order payment types
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentTypes(Request $request)
    {
        $paymentTypes = collect(Order::$paymentTypes);
        return response()->json($paymentTypes);
    }

    /**
     * Search order based on email & dealer.
     *
     * @param SearchOrdersRequest|Request $request
     * @return \Illuminate\Http\Response
     */

    public function search(SearchOrdersRequest $request)
    {
        try {
            $data = $request->all();
            if (isset($data['id'])) {
                $order = Order::with('order_reference')
                              ->whereId($data['id'])
                              ->select('uuid', 'dealer_notes', 'updated_at', 'status_id', 'reference_id')
                              ->first();
                return response()->json($order);
            }

            $inputs = $request->only('customer', 'dealer_id');
            $orders = Order::with('order_reference')
                ->where('dealer_id', $inputs['dealer_id'])
                ->whereHas('order_reference', function ($query) use ($inputs) {
                    if (array_key_exists('email', $inputs['customer']))
                        $query->where('email', 'like', "%{$inputs['customer']['email']}%");

                    if (array_key_exists('first_name', $inputs['customer']))
                        $query->where('first_name', 'like', "%{$inputs['customer']['first_name']}%");

                    if (array_key_exists('last_name', $inputs['customer']))
                        $query->where('last_name', 'like', "%{$inputs['customer']['last_name']}%");
                })
                ->select('uuid', 'dealer_notes', 'updated_at', 'status_id', 'reference_id')
                ->get();

            if ($orders->isEmpty()) {
                return response()->json(['There are no orders for provided customer.'], 422);
            }

            return response()->json($orders);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Store a newly created resource in storage (from dealer form).
     * + Calculations
     *
     * @param SaveDealerOrderRequest $request
     * @param OrderService           $orderService
     * @param OrderDealerFormService $orderDealerFormService
     * @return \Illuminate\Http\Response
     */
    public function saveDealerOrder(
        SaveDealerOrderRequest $request,
        OrderService $orderService,
        OrderDealerFormService $orderDealerFormService)
    {
        try {
            $order = $orderDealerFormService->toModel($request->all());
            $order = $orderService->saveDealerOrder($order);
            $resState = [
                'state' => [
                    'id' => $order->uuid,
                    'dealer_notes' => $order->dealer_notes,
                    'note_admin' => $order->note_admin,
                    'note_dealer' => $order->note_dealer,
                    'status' => $order->status,
                    'status_id' => $order->status_id,
                    'updated_at' => $order->updated_at->format('m-d-Y H:i:s T'),
                ],
                'customer' => [
                    'email' => $order->order_reference->email,
                    'first_name' => $order->order_reference->first_name,
                    'last_name' => $order->order_reference->last_name,
                ],
            ];
            return $resState;
        } catch (BusinessException $e) {
            Log::error($e);
            return response()->json([$e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource. (for dealer order form now)
     *
     * @param  int                   $id
     * @param ShowOrderRequest       $request
     * @param OrderDealerFormService $orderDealerFormService
     * @return \Illuminate\Http\Response
     */
    public function getDealerOrder($id, ShowOrderRequest $request, OrderDealerFormService $orderDealerFormService)
    {
        try {
            $order = Order::where('uuid', $id)
                ->with([
                    'dealer',
                    'dealer.location',
                    'files' => function ($query) {
                        $query->whereIn('category_id', [
                            'signed_order_documents',
                            'signed_building_configuration',
                            'signed_neighbor_release',
                            'e_signed_order_documents',
                            'driver_license',
                            'signed_deposit_receipt',
                        ]);
                    },
                    'order_reference',
                    'building',
                    'building.building_package',
                    'building.building_package.options',
                    'building.building_package.building_model',
                    'building.building_options',
                    'building.building_options.option',
                    'building.building_options.option.category',
                    'building.building_options.option_color',
                    'building.building_options.option_color.color',
                    'building.building_options.option.allowable_colors',
                    'building.building_options.option.allowable_models',
                    'building.building_model',
                    'building.building_model.style',
                    'building.building_model.style.building_models',
                ])
                ->firstOrFail();

            $adjustedOrder = $orderDealerFormService->toArray($order);
            return response()->json($adjustedOrder);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * @param DealerOrderStatusRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDealerOrderStatus(DealerOrderStatusRequest $request): JsonResponse
    {
        try {
            $status = $this->order->where('uuid', $request->get('id'))->firstOrFail(['status_id']);
        } catch (Exception $e) {
            return $this->returnJsonErrorResponse();
        }

        return response()->json($status);
    }

    /**
     * Generate document (used for dealer order form now)
     * @param                              $id
     * @param                              $categoryId
     * @param GenerateOrderDocumentRequest $request
     * @param OrderDealerDocuments         $orderDealerDocuments
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDocument($id,
                                     $categoryId,
                                     GenerateOrderDocumentRequest $request,
                                     OrderDealerDocuments $orderDealerDocuments)
    {
        try {
            $order = Order::where('uuid', $id)->firstOrFail();
            $save = $request->input('save');

            $pdfDocument = $orderDealerDocuments->generateDocument($order, $categoryId);

            if ($save) {
                $files = $orderDealerDocuments->saveDocumentAs($order, $pdfDocument, $categoryId);
            } else {
                $response = $orderDealerDocuments->downloadDocument($order, $pdfDocument, $categoryId);

                // cookie required for correct detecting the moment on client-side, that
                // file is successfully started transfering
                // https://stackoverflow.com/questions/1106377/detect-when-browser-receives-file-download/4168965#4168965
                if ($request->token) {
                    $response->withCookie(cookie($request->token, 1, 1, '/', null, false, false));
                }

                return $response;
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['Order is not found.'], 422);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * /customer-order-form
     * @param CustomerOrderRequest     $request
     * @param OrderPdfService          $orderPdfService
     * @param OrderCustomerFormService $orderCustomerFormService
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerOrder(CustomerOrderRequest $request, OrderPdfService $orderPdfService, OrderCustomerFormService $orderCustomerFormService)
    {
        $order = $orderCustomerFormService->toModel($request->all());
        $pdf = $orderPdfService->getCustomerForm($order);
        $inputs = $request->all();

        $mailResult = Mail::send('emails.customer-order', [
            'customer' => $inputs['customer'],
            'contact_type' => $inputs['contact_type'],
            'contact_time' => $inputs['contact_time'],
        ], function ($message) use ($pdf, $inputs) {
            $fullName = $inputs['customer']['first_name'] . ' ' . $inputs['customer']['last_name'];

            if (app()->environment('production')) {
                $message->cc('info@urbanshedconcepts.com');
            }

            $message->to($inputs['customer']['email'], $fullName)->subject('Shed Quote');
            $message->attachData(Storage::disk('local')->get($pdf->path), 'order.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="quote.pdf"',
            ]);
        });

        if (!Mail::failures()) {
            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email is not sent',
        ]);
    }

    public function exportCsv(IndexOrderRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Order());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $result = $abAssistant->apply()->get()->toArray();

        $header = $this->getExportDataHeader($result[0]);

        Excel::create('export' . date("Ymd-H:i:s"), function ($excel) use ($result, $header) {
            $excel->sheet('Orders Export ' . date("Y-m-d"), function ($sheet) use ($result, $header) {
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

    public function exportXls(IndexOrderRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Order());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $result = $abAssistant->apply()->get()->toArray();

        $header = $this->getExportDataHeader($result[0]);

        Excel::create('export' . date("Ymd-H:i:s"), function ($excel) use ($result, $header) {
            $excel->sheet('Orders Export ' . date("Y-m-d"), function ($sheet) use ($result, $header) {
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
            $header['id'] = 'Order ID';
        }
        if (isset($item['created_at'])) {
            $header['date_created'] = 'Date Created';
        }
        if (isset($item['status'])) {
            $header['status'] = 'Status';
        }
        if (isset($item['order_reference']) && array_key_exists('customer_name', $item['order_reference'])) {
            $header['customer'] = 'Customer';
        }
        if (isset($item['order_type'])) {
            $header['order_type'] = 'Order Type';
        }
        if (isset($item['building']) && array_key_exists('serial_number', $item['building'])) {
            $header['serial_number'] = 'Building SN';
        }
        if (isset($item['dealer'])) {
            $header['dealer'] = 'Dealer';
        }
        if (isset($item['retail'])) {
            $header['retail'] = 'Retail';
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
        if (isset($header['status'])) {
            $data[] = $item['status']['title'];
        }
        if (isset($header['customer'])) {
            $data[] = $item['order_reference']['customer_name'];
        }
        if (isset($header['order_type'])) {
            $data[] = $item['order_type']['title'];
        }
        if (isset($header['serial_number'])) {
            $data[] = $item['building']['serial_number'];
        }
        if (isset($header['dealer'])) {
            $data[] = $item['dealer']['business_name'];
        }
        if (isset($header['retail'])) {
            $data[] = $item['retail'];
        }

        return $data;
    }

    public function getLeadContacts(Request $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new OrderContact());
        $abAssistant->setArrayQuery($request->all());
        $query = $abAssistant->apply()->getQuery();
        $result = $abAssistant->setQuery($query)->paginate(
            request('page'),
            request('per_page') ? request('per_page') : $this->getPerPageSetting()
        );

        // retrieve expired date from created_at + initial_contact_eligibility
        $setting = new Setting();
        $eligibility = $setting
            ->where('id', 'initial_contact_eligibility')
            ->pluck('value')
            ->first();
        foreach ($result['data'] as $key => $value) {
            if (!empty($value['created_at'])) {
                $expired_at = Carbon::createFromTimestamp(strtotime($value['created_at']))->addDays($eligibility);
                $result['data'][ $key ]['expired_at'] = $expired_at;
            }
        }

        return response()->json($result);
    }

    public function getCustomer()
    {
        $orderContact = new OrderContact();
        $customers = $orderContact
            ->whereHas('customer')
            ->with(['customer' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }])
            ->get();
        $customerData = [];
        foreach ($customers as $key => $value) {
            $customerData[$value->customer->id] = [
                'id' => $value->customer->id,
                'title' => $value->customer->first_name . ' ' . $value->customer->last_name,
            ];
        }
        return response()->json($customerData);
    }
}

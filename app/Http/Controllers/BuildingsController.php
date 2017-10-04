<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetWorkBuildingFormRequest;
use App\Services\Orders\OrderPdfService;
use Illuminate\Support\Facades\Artisan;
use PDF;
use DB;
use Auth;
use Event;
use Store;
use Log;
use Storage;
use Helper;
use Exception;

use App\Models\Building;
use App\Models\Order;
use App\Models\Dealer;
use App\Http\Requests;

use App\Http\Requests\GetInventoryFormRequest;
use App\Http\Requests\GetWorkFormRequest;
use App\Http\Requests\GetInitialInventoryFormRequest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingsController extends Controller
{
    private $route_name = 'buildings';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Dashboard'],
                ['url' => $this->route_name, 'page' => 'Buildings']
            ],
            'title' => 'Buildings',
            'subtitle' => 'Buildings',
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
        // if ( ! RequestUtils::havePermission($this->route_name, 'any'))
        // {
        //     return redirect('/')
        //         ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        return view($this->route_name . '.vue-index')->withParams($this->params);
    }

    /**
     * @param $id
     * @param GetInventoryFormRequest $request
     * @param OrderPdfService $orderPdfService
     * @return mixed
     */
    public function pdfInventoryForm($id, GetInventoryFormRequest $request, OrderPdfService $orderPdfService)
    {
        try {
            $building = Building::findOrFail($id)->load([
                'building_package.options',
                'building_package.building_model',
                'building_options.option.category',
                'building_options.option_color.color',
                'building_model.style.building_models'
            ]);
            $dealer = Store::get('dealer')->load('location');

            $pdf = $orderPdfService->getInventoryForm($building, $dealer);
            return $pdf->stream();
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * @param $id
     * @param GetWorkFormRequest $request
     * @param OrderPdfService $orderPdfService
     * @return mixed
     */
    public function pdfWorkForm($id, GetWorkFormRequest $request, OrderPdfService $orderPdfService)
    {
        try {
            $order = Order::with(['building', 'dealer'])
                ->where('building_id', $id)
                ->orderBy('id', 'desc')
                ->firstOrFail()
                ->load([
                    'dealer',
                    'building.building_package.options',
                    'building.building_package.building_model',
                    'building.building_options.option.category',
                    'building.building_options.option_color.color',
                    'building.building_model.style.building_models'
                ]);

            $pdf = $orderPdfService->getWorkForm($order);
            return $pdf->stream();
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * @param $id
     * @param GetWorkBuildingFormRequest $request
     * @param OrderPdfService $orderPdfService
     * @return mixed
     */
    public function pdfWorkBuildingForm($id, GetWorkBuildingFormRequest $request, OrderPdfService $orderPdfService)
    {
        try {
            $building = Building::findOrFail($id);
            $building->load([
                'building_package.options',
                'building_package.building_model',
                'building_options.option.category',
                'building_options.option_color.color',
                'building_model.style.building_models'
            ]);

            $dealer = Dealer::findOrFail(1);
            $order = new Order;
            $order->setRelation('building', $building);

            $pdf = $orderPdfService->getWorkBuildingForm($order, $dealer);
            return $pdf->stream();
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * @param $id
     * @param GetInitialInventoryFormRequest $request
     * @param OrderPdfService $orderPdfService
     * @return mixed
     */
    public function pdfInitialInventoryForm($id, GetInitialInventoryFormRequest $request, OrderPdfService $orderPdfService)
    {
        try {
            $building = Building::with([
                'building_package',
                'building_package.building_model',
                'building_options',
                'building_options.option',
                'building_options.option_color',
                'building_options.option',
                'building_options.option.category',
            ])->findOrFail($id);

            $dealer = Dealer::findOrFail(1);
            $dealer->tax_rate = 0;

            $pdf = $orderPdfService->getInitialInventoryForm($building, $dealer);
            return $pdf->stream();
        } catch (Exception $e) {
            Log::error($e);
        }
    }
    
    public function import()
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Import Buildings']);
        $this->params['subtitle'] = 'Import Buildings';

        return view($this->route_name . '.import')->withParams($this->params);
    }
}

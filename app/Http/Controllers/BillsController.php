<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App\Models\User;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Building;
use App\Models\BuildingHistory;
use App\Models\BuildingLocation;

use App\Jobs\CreateBills;
use App\Http\Requests\AddBillRequest;
use Mockery\CountValidator\Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Collection;

use App\Services\Reports\BillReportService;

class BillsController extends Controller
{
    private $route_name = 'bills';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Bills']
            ],
            'title' => 'Bills',
            'subtitle' => 'Bills',
            'search' => 'yes',
            'filter' => '',
            'items_per_page' => 500,
            'route_name' => $this->route_name,
            'route' => '/' . $this->route_name,
            'data' => null,
        ];
    }

    public function index()
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Create']);

        $users[0] = ['value' => null, 'display' => 'None', 'options' => [ 'disabled' => true ]];
        $users['all'] = ['value' => 'all', 'display' => '- All -'];
        User::get()->each(function($user, $key) use (&$users) {
            $users[$user->id] = [
                'value' => $user->id,
                'display' => $user->first_name.' '.$user->last_name
            ];
        });

        $this->params['data']['users'] = $users;
        $this->params['subtitle'] = 'Create Bill';
        return view($this->route_name . '.index')->withParams($this->params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Create']);

        $users[0] = ['value' => null, 'display' => 'None', 'options' => [ 'disabled' => true ]];
        $users['all'] = ['value' => 'all', 'display' => '- All -'];
        User::get()->each(function($user, $key) use (&$users) {
            $users[$user->id] = [
                'value' => $user->id,
                'display' => $user->first_name.' '.$user->last_name
            ];
        });

        $this->params['data']['users'] = $users;
        $this->params['subtitle'] = 'Create Bill';
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddBillRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddBillRequest $request)
    {
        try
        {
            $userID = $request->get('user_id');
            $billsInfo = $this->dispatch(new CreateBills($userID));

            if ( $billsInfo['summary']['count'] > 0 )
                $message[] = 'Bills successfully generated.';
            else
                $message[] = 'There is no unbilled changes.';

            return response()->json(array(
                'success' => true,
                'message' => $message,
                'payload' => $billsInfo
            ));
        } catch (Exception $e)
        {
            return response()->json(array(
                'success' => false,
                'message' => [ 'Sorry, there was an error saving the record.' ]
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'View Bill']);

        /*Relation::morphMap([
            'status' => BuildingHistory::class,
            'location' => BuildingLocation::class,
        ]);*/

        $this->params['data']['item'] = $bill = Bill::with([
            'user',
            'building_history.building',
            'building_history.contractor',
            'building_history.building_status',
            'building_locations.driver',
            'building_locations.building'
        ])->findOrFail($id);

        $this->params['subtitle'] = 'Bill #' . $bill->number;

        return view($this->route_name . '.details')->withParams($this->params);
    }

    /**
     * Display a listing of the resource.
     *
     * @param ReportService|BillReportService $billReport
     * @return \Illuminate\Http\Response
     */
    public function report(BillReportService $billReport)
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Report']);

        $rules['bill_report'] = $billReport->getRules();

        $this->params['rules'] = $rules;
        $this->params['subtitle'] = 'Bill Report';
        return view($this->route_name . '.report')->withParams($this->params);
    }
}

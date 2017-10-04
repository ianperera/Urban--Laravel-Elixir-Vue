<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App\Models\User;
use App\Models\Delivery;

// use App\Http\Requests\AddDeliveryRequest;
use Exception;

class DeliveriesController extends Controller
{
    private $route_name = 'deliveries';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/deliveries/', 'page' => 'Deliveries']
            ],
            'title' => 'Deliveries',
            'subtitle' => 'Deliveries',
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
        $this->params['subtitle'] = 'Create Delivery';
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddBillRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store($request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }
}

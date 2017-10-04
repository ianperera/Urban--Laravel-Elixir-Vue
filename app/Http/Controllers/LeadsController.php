<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
/*
use App\Http\Requests\AddDealerRequest;
use App\Http\Requests\EditDealerRequest;
use App\Http\Requests\UpdateDealerRequest;
use App\Http\Requests\DeleteDealerRequest;*/

class LeadsController extends Controller
{

    private $route_name = 'leads';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Dashboard'],
                ['url' => $this->route_name, 'page' => 'Leads']
            ],
            'title' => 'Leads',
            'subtitle' => 'Leads',
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
        $this->params['filter'] = $request->input('filter');
        $this->params['data']['items'] = Lead::filteredPaginate($this->params['filter'], $this->params['items_per_page']);

        return view($this->route_name . '.index')->withParams($this->params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'New Lead']);

        $this->params['subtitle'] = 'New Lead';
        return view($this->route_name . '._form')->withParams($this->params);
    }
    
}

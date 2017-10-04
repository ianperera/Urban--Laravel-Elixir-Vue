<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{

    private $route_name = '';

    private $params = [];

    public function __construct()
    {

        $this->middleware('auth');

        $this->params = [
            'breadcrumbs' => null,
            'title' => 'Dashboard',
            'subtitle' => '',
            'panel_color' => 'panel-midnightblue',
            'search' => 'YES',
            'filter' => '',
            'items_per_page' => 500,
            'route_name' => $this->route_name,
            'route' => '/'.$this->route_name,
            'data' => null,
        ];
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {
        return view('dashboard.index')->withParams($this->params);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrucksController extends Controller
{
    private $route_name = 'trucks';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => $this->route_name, 'page' => 'Trucks']
            ],
            'title' => 'Trucks',
            'subtitle' => 'Trucks',
            'search' => 'yes',
            'filter' => '',
            'items_per_page' => 500,
            'route_name' => $this->route_name,
            'route' => '/' . $this->route_name,
            'data' => null,
        ];
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('spa')->withParams($this->params);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrailersController extends Controller
{
    private $route_name = 'trailers';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => $this->route_name, 'page' => 'Trailers']
            ],
            'title' => 'Trailers',
            'subtitle' => 'Trailers',
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BuildingPackagesCategoriesController extends Controller
{
    private $route_name = 'building-package-categories';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [['url' => '/', 'page' => 'Dashboard'], ['url' => $this->route_name, 'page' => 'Building Package Categories']],
            'title' => 'Building Package Categories',
            'subtitle' => 'Building Package Categories',
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
}

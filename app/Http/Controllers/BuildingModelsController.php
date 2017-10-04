<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Color;
use Log;
use Event;
use Exception;
use App\Models\Style;
use App\Models\BuildingModel;
use App\Models\BuildingStatus;
use App\Models\ModelBuildingStatus;
use App\Http\Requests;
use App\Http\Requests\AddBuildingModelRequest;
use App\Http\Requests\UpdateBuildingModelRequest;
use App\Events\BuildingModelWasUpdated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuildingModelsController extends Controller
{
    private $route_name = 'building-models';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [['url' => '/', 'page' => 'Dashboard'], ['url' => $this->route_name, 'page' => 'Building Models']],
            'title' => 'Building Models',
            'subtitle' => 'Building Models',
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

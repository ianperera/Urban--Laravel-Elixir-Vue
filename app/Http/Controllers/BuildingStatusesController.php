<?php

namespace App\Http\Controllers;

use App\Models\BuildingStatus;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\AddBuildingStatusRequest;
use App\Http\Requests\EditBuildingStatusRequest;
use App\Http\Requests\UpdateBuildingStatusRequest;
use App\Http\Requests\DeleteBuildingStatusRequest;
use Illuminate\Support\Facades\Response;

class BuildingStatusesController extends Controller
{

    private $route_name = 'building_statuses';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Dashboard'],
                ['url' => $this->route_name, 'page' => 'Building Statuses']
            ],
            'title' => 'Building Statuses',
            'subtitle' => 'Building Statuses',
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
        $this->params['status_type'] = 'all' ;
        $this->params['data']['status_types'] = [
            'build' => 'Build Status'
        ];

        $buildingStatuses = BuildingStatus::orderBy('priority', 'asc');

        if( isset($this->params['data']['status_types'][$request->input('status_type')]) )
        {
            $this->params['status_type'] = $request->input('status_type');
            $buildingStatuses->where('type',  $request->input('status_type'));
        }

        $this->params['data']['items'] = $buildingStatuses->filteredPaginate($this->params['filter'], $this->params['items_per_page']);

        return view('spa')->withParams($this->params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'New Building Status']);

        $this->params['subtitle'] = 'New Building Status';
        $this->params['data']['status_types'] = [
            'build' => 'Build Status',
        ];

        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddBuildingStatusRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddBuildingStatusRequest $request)
    {
        try
        {
            $buildingStatusData = $request->only(['type', 'name', 'priority', 'is_active']);
            $building = BuildingStatus::create($buildingStatusData);
        } catch (QueryException $e)
        {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error saving the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Building Status added succcessfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param EditBuildingStatusRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditBuildingStatusRequest $request,  $id)
    {
        // get data which has got through validator
        $requestedBuildingStatus = $request->data['requestedBuildingStatus'];

        $this->params['data']['item'] = $item = $requestedBuildingStatus;
        $this->params['data']['status_types'] = [
            'build' => 'Build Status',
        ];

        $this->params['subtitle'] = 'Edit Building Status ' . $item->name;
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Edit Building Status ' . $item->name]);

        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBuildingStatusRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBuildingStatusRequest $request, $id)
    {
        try
        {
            $requestedBuildingStatus = $request->data['requestedBuildingStatus'];
            $buildingStatusData = $request->all();
            $requestedBuildingStatus->update($buildingStatusData);
        } catch (QueryException $e)
        {
            if ( $request->ajax() ) return response()->json(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.'], 500);
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        if ( $request->ajax() ) return response()->json(['title' => 'Success', 'type' => 'success', 'text' => 'BuildingStatus successfully updated.']);
        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'BuildingStatus successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteBuildingStatusRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteBuildingStatusRequest $request, $id)
    {
        try
        {
            // get data which has got through validator/request
            $buildingStatus = $request->data['requestedBuildingStatus'];
            $buildingStatus->delete();
        }
        catch (QueryException $e)
        {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Building Status successfully deleted.']);
    }
}

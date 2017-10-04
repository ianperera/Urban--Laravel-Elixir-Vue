<?php

namespace App\Http\Controllers;

use App\Services\Locations\LocationService;
use Store;
use App\Models\Location;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\Locations\AddLocationRequest;
use App\Http\Requests\Locations\EditLocationRequest;
use App\Http\Requests\Locations\UpdateLocationRequest;
use App\Http\Requests\Locations\DeleteLocationRequest;

class LocationsController extends Controller
{

    private $route_name = 'locations';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Dashboard'],
                ['url' => $this->route_name, 'page' => 'Locations']
            ],
            'title' => 'Locations',
            'subtitle' => 'Locations',
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
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'New Location']);

        $this->params['subtitle'] = 'New Location';

        $locationCategories = [];
        foreach (Location::$categories as $category) {
            $locationCategories[$category['id']] = $category['name'];
        }
        $this->params['data']['categories'] = $locationCategories;

        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddLocationRequest $request
     * @param LocationService    $locationService
     * @return \Illuminate\Http\Response
     */
    public function store(AddLocationRequest $request, LocationService $locationService)
    {
        try {
            $locationData = $request->only(['name', 'address', 'country', 'city', 'state', 'zip', 'latitude', 'longitude', 'category', 'is_active', 'notes']);
            $locationService->create($locationData);
        } catch (QueryException $e) {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error saving the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Location added succcessfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param EditLocationRequest $request
     * @param  int                $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditLocationRequest $request, $id)
    {
        // get data which has got through validator
        $requestedLocation = Store::get('requestedLocation');

        $locationCategories = [];
        foreach (Location::$categories as $category) {
            $locationCategories[$category['id']] = $category['name'];
        }

        $this->params['data']['item'] = $item = $requestedLocation;
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Edit Location ' . $item->serial_number]);
        $this->params['subtitle'] = 'Edit Location ' . $item->name;
        $this->params['data']['categories'] = $locationCategories;
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLocationRequest $request
     * @param  int                  $id
     * @param LocationService       $locationService
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLocationRequest $request, $id, LocationService $locationService)
    {
        // get data which has got through validator
        $requestedLocation = Store::get('requestedLocation');

        try {
            $buildingLocationData = $request->only(['name', 'address', 'country', 'city', 'state', 'zip', 'latitude', 'longitude', 'category', 'is_active']);
            $locationService->update($requestedLocation, $buildingLocationData);
        } catch (QueryException $e) {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Location successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteLocationRequest $request
     * @param  int                  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteLocationRequest $request, $id)
    {
        try {
            // get data which has got through validator
            $location = Store::get('location');
            $location->delete();
        } catch (QueryException $e) {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Location successfully deleted.']);
    }
}

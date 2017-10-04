<?php

namespace App\Http\Controllers;

use Store;
use App\Models\Setting;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\AddSettingRequest;
use App\Http\Requests\EditSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Requests\DeleteSettingRequest;

class SettingsController extends Controller
{

    private $route_name = 'settings';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Dashboard'],
                ['url' => $this->route_name, 'page' => 'Settings']
            ],
            'title' => 'Settings',
            'subtitle' => 'Settings',
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
        // $this->params['filter'] = $request->input('filter');
        // $this->params['data']['items'] = Setting::filteredPaginate($this->params['filter'], $this->params['items_per_page']);

        return view('spa')->withParams($this->params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'New Setting']);

        $this->params['subtitle'] = 'New Setting';

        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddSettingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddSettingRequest $request)
    {
        try
        {
            $settingData = $request->only(['id', 'title', 'description', 'value']);
            $setting = Setting::create($settingData);
        } catch (QueryException $e)
        {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error saving the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Setting added succcessfully.']);
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
     * @param EditSettingRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditSettingRequest $request,  $id)
    {
        // get data which has got through validator
        $setting = Store::get('setting');
        
        $this->params['data']['item'] = $item = $setting;
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Edit Setting ' . $item->title]);
        $this->params['subtitle'] = 'Edit Setting ' . $item->title;
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSettingRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingRequest $request, $id)
    {
        // get data which has got through validator
        $setting = Store::get('setting');

        try
        {
            $settingData = $request->only(['id', 'title', 'description', 'value']);
            $setting->update($settingData);
        } catch (QueryException $e)
        {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Setting successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteSettingRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteSettingRequest $request, $id)
    {
        try
        {
            // get data which has got through validator
            $setting = Store::get('setting');
            $setting->delete();
        } catch (QueryException $e)
        {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Setting successfully deleted.']);
    }
}

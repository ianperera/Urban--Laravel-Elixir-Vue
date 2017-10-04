<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Plant;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlantsController extends Controller
{
    private $route_name = 'plants';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [['url' => '/', 'page' => 'Dashboard'], ['url' => $this->route_name, 'page' => 'Plants']],
            'title' => 'Plants',
            'subtitle' => 'Plants',
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
        // if(!RequestUtils::havePermission($this->route_name, 'create'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'New Plant']);
        $this->params['subtitle'] = 'New Plant';
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if ( ! RequestUtils::havePermission($this->route_name, 'create'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        try {
            Plant::create($request->all());
        } catch (QueryException $e) {
            return redirect($this->route_name)
                ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error saving the record.']);
        }

        return redirect($this->route_name)
            ->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Plant added succcessfully.']);
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // if(!RequestUtils::havePermission($this->route_name, 'edit'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Edit Plant']);
        $this->params['subtitle'] = 'Edit Plant';
        $this->params['data']['item'] = Plant::findOrFail($id);

        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // if(!RequestUtils::havePermission($this->route_name, 'edit'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        $row = Plant::findOrFail($id);

        try {
            $row->update($request->all());
        } catch (QueryException $e) {
            return redirect($this->route_name)
                ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)
            ->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Plant successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // if(!RequestUtils::havePermission($this->route_name, 'delete'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        $row = Plant::findOrFail($id);

        try {
            $row->delete();
        } catch (QueryException $e) {
            return redirect($this->route_name)
                ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error deleting the record.']);
        }
        return redirect($this->route_name)
            ->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Plant successfully deleted.']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Style;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;

class StylesController extends Controller
{
    private $route_name = 'styles';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Dashboard'],
                ['url' => $this->route_name, 'page' => 'Styles']
                ],
            'title' => 'Styles',
            'subtitle' => 'Styles',
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
        // if ( ! RequestUtils::havePermission($this->route_name, 'any'))
        // {
        //     return redirect('/')
        //         ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        // $this->params['filter'] = $request->input('filter');
        // $this->params['data']['items'] = Style::filteredPaginate($this->params['filter'], $this->params['items_per_page']);

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

        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'New Style']);
        $this->params['subtitle'] = 'New Style';
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if ( ! RequestUtils::havePermission($this->route_name, 'create'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        try
        {
            $data = $request->all();

            if (isset($data['short_code']))
            {
                $data['short_code'] = strtoupper($data['short_code']);
            }

            Style::create($data);
        }
        catch (QueryException $e)
        {
            return redirect($this->route_name)
                ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error saving the record.']);
        }

        return redirect($this->route_name)
                ->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Style added succcessfully.']);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // if(!RequestUtils::havePermission($this->route_name, 'edit'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Edit Style']);
        $this->params['subtitle'] = 'Edit Style';
        $this->params['data']['item'] = Style::findOrFail($id);

        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // if(!RequestUtils::havePermission($this->route_name, 'edit'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        $row = Style::findOrFail($id);

        try
        {
            $data = $request->all();

            if (isset($data['short_code']))
            {
                $data['short_code'] = strtoupper($data['short_code']);
            }

            $row->update($data);
        }
        catch (QueryException $e)
        {
            return redirect($this->route_name)
                ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)
                ->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Style successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // if(!RequestUtils::havePermission($this->route_name, 'delete'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        $row = Style::findOrFail($id);

        try
        {
            $row->delete();
        }
        catch (QueryException $e)
        {
            return redirect($this->route_name)
                ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error deleting the record.']);
        }
        return redirect($this->route_name)
                ->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Style successfully deleted.']);
    }
}

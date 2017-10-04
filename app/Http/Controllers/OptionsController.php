<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddOptionRequest;
use App\Http\Requests\EditOptionRequest;
use App\Http\Requests\UpdateOptionRequest;
use App\Models\BuildingModel;
use App\Models\Option;
use App\Http\Requests;
use App\Models\OptionCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OptionsController extends Controller
{
    private $route_name = 'options';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [['url' => '/', 'page' => 'Dashboard'], ['url' => $this->route_name, 'page' => 'Options']],
            'title' => 'Options',
            'subtitle' => 'Options',
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
        // if(!RequestUtils::havePermission($this->route_name, 'create'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'New Option']);

        $buildingModels = BuildingModel::active()
            ->with('style')->whereHas('style', function ($query) {
                $query->where('is_active', 'yes');
            })->get();

        $buildingModelsPerStyle = [];
        $buildingModels->each(function($buildingModel) use(&$buildingModelsPerStyle) {
            if( !isset($buildingModelsPerStyle[$buildingModel->style->name]) )
                $buildingModelsPerStyle[$buildingModel->style->name] = [];

            $buildingModelsPerStyle[$buildingModel->style->name][$buildingModel->id] = $buildingModel->name;
        });

        $this->params['data']['categories'] = OptionCategory::all()->map(function($item, $key) {
            $item->name = "{$item->name} ({$item->group})";
            return $item;
        })->pluck('name', 'id')->toArray();

        $this->params['data']['building_models_per_style'] = $buildingModelsPerStyle;
        $this->params['subtitle'] = 'New Option';
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddOptionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddOptionRequest $request)
    {
        // if ( ! RequestUtils::havePermission($this->route_name, 'create'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        try
        {
            $optionData = $request->only(['name', 'description', 'unit_price', 'allowable_models_id', 'category_id']);
            $option = Option::create($optionData);

            if ( !isset($optionData['allowable_models_id']))
                $optionData['allowable_models_id'] = [];

            $option->allowable_models()->sync($optionData['allowable_models_id']);
        } catch (QueryException $e)
        {
            return redirect($this->route_name)
                ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error saving the record.']);
        }

        return redirect($this->route_name)
                ->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Option added succcessfully.']);
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
     * @param EditOptionRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditOptionRequest $request, $id)
    {
        // if(!RequestUtils::havePermission($this->route_name, 'edit'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Edit Option']);
        $option = Option::with(['allowable_models'])->findOrFail($id);
        $buildingModels = BuildingModel::active()
            ->with('style')->whereHas('style', function ($query) {
                $query->where('is_active', 'yes');
            })->get();

        $buildingModelsPerStyle = [];
        $buildingModels->each(function($buildingModel) use(&$buildingModelsPerStyle) {
            if( !isset($buildingModelsPerStyle[$buildingModel->style->name]) )
                $buildingModelsPerStyle[$buildingModel->style->name] = [];

            $buildingModelsPerStyle[$buildingModel->style->name][$buildingModel->id] = $buildingModel->name;
        });

        $this->params['data']['categories'] = OptionCategory::all()->map(function($item, $key) {
            $item->name = "{$item->name} ({$item->group})";
            return $item;
        })->pluck('name', 'id')->toArray();

        $this->params['data']['item'] = $option;
        $this->params['data']['building_models_per_style'] = $buildingModelsPerStyle;
        $this->params['subtitle'] = 'Edit Option';
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOptionRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOptionRequest $request, $id)
    {
        // if(!RequestUtils::havePermission($this->route_name, 'edit'))
        // {
        //     return redirect($this->route_name)
        //             ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'No tiene permiso para realizar la acción']);
        // }

        $option = $request->validator->getStore('option');

        try
        {
            $optionData = $request->only(['name', 'description', 'unit_price', 'allowable_models_id', 'category_id']);
            $option->update($optionData);

            if ( !isset($optionData['allowable_models_id']))
                $optionData['allowable_models_id'] = [];

            $option->allowable_models()->sync($optionData['allowable_models_id']);
        } catch (QueryException $e)
        {
            return redirect($this->route_name)
                ->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)
                ->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Option successfully updated.']);
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

        $row = Option::findOrFail($id);

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
                ->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Option successfully deleted.']);
    }
}

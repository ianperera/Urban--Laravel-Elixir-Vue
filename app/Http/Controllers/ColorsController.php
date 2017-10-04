<?php

namespace App\Http\Controllers;

use Exception;
use Log;
use Store;
use App\Models\Color;
use App\Models\Option;
use App\Models\BuildingModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\AddColorRequest;
use App\Http\Requests\EditColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Http\Requests\DeleteColorRequest;

class ColorsController extends Controller
{

    private $route_name = 'colors';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Dashboard'],
                ['url' => $this->route_name, 'page' => 'Colors']
            ],
            'title' => 'Colors',
            'subtitle' => 'Colors',
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
        // $this->params['data']['items'] = Color::with('option')->filteredPaginate($this->params['filter'], $this->params['items_per_page']);

        return view('spa')->withParams($this->params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'New Color']);

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

        $this->params['subtitle'] = 'New Color';
        $this->params['data']['types'] = Color::$types;
        $this->params['data']['options'] = Option::all()->pluck('name', 'id')->toArray();
        $this->params['data']['building_models_per_style'] = $buildingModelsPerStyle;

        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddColorRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddColorRequest $request)
    {
        try
        {
            $inputs = $request->all();
            $inputs['option_id'] = $inputs['option_id'] !== '' ? $inputs['option_id'] : null;
            $inputs['hex'] = $inputs['hex'] !== '' ? $inputs['hex'] : null;
            $color = Color::create($inputs);
            $allowableModels = $request->input('allowable_models_id') ?? [];
            $color->allowable_models()->sync($allowableModels);
        } catch (Exception $e)
        {
            Log::error($e);
            return redirect()->back()->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error saving the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Color added succcessfully.']);
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
     * @param EditColorRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditColorRequest $request,  $id)
    {
        // get data which has got through validator
        $color = Store::get('color');
        $color->load('allowable_models');

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

        $this->params['data']['item'] = $item = $color;
        $this->params['data']['types'] = Color::$types;
        $this->params['data']['options'] = Option::all()->pluck('name', 'id')->toArray();
        $this->params['data']['building_models_per_style'] = $buildingModelsPerStyle;
        $this->params['subtitle'] = 'Edit Color ' . $item->title;
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Edit Color ' . $item->title]);
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateColorRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateColorRequest $request, $id)
    {
        // get data which has got through validator
        $color = Store::get('color');
        try
        {
            $inputs = $request->only(['type', 'name', 'hex', 'url', 'use_body', 'use_trim', 'use_roof', 'option_id']);
            $inputs['option_id'] = $inputs['option_id'] !== '' ? $inputs['option_id'] : null;
            $inputs['hex'] = $inputs['hex'] !== '' ? $inputs['hex'] : null;
            $inputs['use_body'] = $inputs['use_body'] ? $inputs['use_body'] : 0;
            $inputs['use_trim'] = $inputs['use_trim'] ? $inputs['use_trim'] : 0;
            $inputs['use_roof'] = $inputs['use_roof'] ? $inputs['use_roof'] : 0;
            $color->update($inputs);

            $allowableModels = $request->input('allowable_models_id') ?? [];
            $color->allowable_models()->sync($allowableModels);
        } catch (Exception $e)
        {
            Log::error($e);
            return redirect()->back()->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect()->back()->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Color successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteColorRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteColorRequest $request, $id)
    {
        try
        {
            // get data which has got through validator
            $color = Store::get('color');
            $color->delete();
        } catch (Exception $e)
        {
            Log::error($e);
            return redirect()->back()->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Color successfully deleted.']);
    }
}

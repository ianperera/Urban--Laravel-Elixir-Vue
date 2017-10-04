<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\IndexColorRequest;

use Store;
use Auth;
use DB;

use App\Models\Color;
use App\Models\Option;
use App\Models\BuildingModel;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddColorRequest;
use App\Http\Requests\DeleteColorRequest;
use App\Http\Requests\UpdateColorRequest;

use App\Services\ArrayBuilder\ArrayBuilderAssistant;
use App\Validators\Validator;

class ColorsController extends Controller
{
    public function __construct()
    {
    }

    public function index(IndexColorRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Color());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $result = $abAssistant
            ->apply()
            ->paginate(
                request('page'),
                request('per_page') ? request('per_page') : $this->getPerPageSetting()
            );
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Color());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $query = $abAssistant->apply()->getQuery();
        try {
            $item = $query->findOrFail($id);
            return response()->json($item);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['Color is not found.'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddColorRequest $request)
    {
        try
        {
            DB::transaction(function() use($request) {
                $colorParams = $request->all();
                if ($colorParams['option_id'] == 'null') $colorParams['option_id'] = null; 
                $color = Color::create($colorParams);

                $allowableModels = $colorParams['allowable_models_id'] ?? [];
                $color->allowable_models()->sync($allowableModels);
            });
            return response()->json(['Color successfully created.']);
        } catch (Exception $e)
        {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
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
        try
        {
            DB::transaction(function() use($request) {
                $colorParams = $request->all();
                if ($colorParams['option_id'] == 'null') $colorParams['option_id'] = null; 
                $color = Store::get('color');
                
                $color->update($colorParams);
                if ($color->id) {
                    if (array_key_exists('allowable_models_id', $colorParams)) {
                        $color->allowable_models()->sync((array) $colorParams['allowable_models_id']);
                    }
                }
            });
            return response()->json(['Color successfully updated.']);
        } catch (Exception $e)
        {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteColorRequest $request)
    {
        try
        {
            // get data which has got through validator
            $color = Store::get('color');
            $color->delete();
            return response()->json(['Color successfully deleted.']);
        } catch (Exception $e)
        {
            return response()->json(['Something went wrong.'], 422);
        }
    }
    
    /**
     * Display a listing of the resource.
     * TODO: should be deprecated
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function colorsByUsage(Request $request)
    {
        $useFlag = $request->route('use_flag');
        $validator = Validator::make(['usage' => $useFlag], ['usage' => 'required|string|alpha|in:body,trim,roof']);
        if ($validator->fails()) return response()->json($validator->errors()->all());

        $col = "use_{$useFlag}";
        $colors = Color::with('allowable_models')->where($col, 1)->get()->keyBy('id');

        $colorsFormat = [];
        $colors->each(function ($color) use(&$colorsFormat) {
            $modelsID = $color->allowable_models->pluck('id')->toArray();
            $color->setRelation('allowable_models', null);

            if (!isset($colorsFormat[$color->id]))
            {
                $colorsFormat[$color->id] = $color->toArray();
                $colorsFormat[$color->id]['availableIn'] = $modelsID;
            } else
            {
                $colorsFormat[$color->id]['availableIn'] = array_merge($colorsFormat[$color->id]['availableIn'], $modelsID);
            }

        });

        return response()->json($colorsFormat);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeFlags(Request $request) {
        $isActiveFlags = Color::$isActive;

        return response()->json($isActiveFlags);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function types(Request $request) {
        $isActiveFlags = Color::$types;

        return response()->json($isActiveFlags);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function options(Request $request) {
        $options = Option::all();

        return response()->json($options);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buildingModels(Request $request) {
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

        return response()->json($buildingModelsPerStyle);
    }
}

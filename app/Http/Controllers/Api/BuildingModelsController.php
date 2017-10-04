<?php

namespace App\Http\Controllers\Api;

use Store;
use DB;
use Auth;

use App\Models\BuildingModel;
use Log;
use Event;
use Exception;

use App\Http\Requests;
use App\Http\Requests\IndexBuildingModelRequest;
use App\Http\Requests\AddBuildingModelRequest;
use App\Http\Requests\UpdateBuildingModelRequest;
use App\Http\Requests\DeleteBuildingModelRequest;
use App\Events\BuildingModelWasUpdated;

use App\Validators\Validator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\ArrayBuilder\ArrayBuilderAssistant;

class BuildingModelsController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @param IndexBuildingModelRequest $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexBuildingModelRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new BuildingModel());
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
     * Get resource
     * @param $id
     * @param Request $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return array
     */
    public function show($id, Request $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new BuildingModel());
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
            return response()->json(['Building model is not found.'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddBuildingModelRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddBuildingModelRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $building_model_params = $request->all();
                $building_model        = BuildingModel::create($building_model_params);
                if ($building_model->id) {
                    if (array_key_exists('allowable_options_id', $building_model_params)) {
                        $building_model->allowable_options()->sync((array)$building_model_params['allowable_options_id']);
                    }

                    // Fire building model was updated
                    Event::fire(new BuildingModelWasUpdated($building_model, [
                        'building_model_status_cost' => $request->input('model_status_cost')
                    ]));
                }
            });
            return response()->json(['Building Model successfully created.']);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBuildingModelRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBuildingModelRequest $request, $id)
    {
        try {
            DB::transaction(function () use ($request) {
                $bildingModelParams = $request->all();

                $bildingModel = Store::get('buildingModel');
                $bildingModel->update($bildingModelParams);
                if ($bildingModel->id) {
                    if (array_key_exists('allowable_options_id', $bildingModelParams)) {
                        $bildingModel->allowable_options()->sync((array)$bildingModelParams['allowable_options_id']);
                    }

                    // Fire building model was updated
                    Event::fire(new BuildingModelWasUpdated($bildingModel, [
                        'building_model_status_cost' => $request->input('model_status_cost')
                    ]));
                }
            });
            return response()->json(['Building Model successfully updated.']);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteBuildingModelRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteBuildingModelRequest $request)
    {
        try {
            // get data which has got through validator
            $buildingModel = Store::get('buildingModel');
            $buildingModel->delete();
            return response()->json(['Building Model successfully deleted.']);
        } catch (Exception $e) {
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeFlags(Request $request)
    {
        $isActiveFlags = BuildingModel::$isActive;

        return response()->json($isActiveFlags);
    }
}

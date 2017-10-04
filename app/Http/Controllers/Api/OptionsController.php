<?php

namespace App\Http\Controllers\Api;
use Log;
use Exception;
use Store;
use DB;
use Auth;

use App\Services\Files\FileService;

use App\Models\File;
use App\Models\Option;
use App\Models\OptionCategory;

use App\Validators\Validator;
use App\Http\Requests;
use App\Http\Requests\IndexOptionRequest;
use App\Http\Requests\OptionCategories\IndexOptionCategoryRequest;
use App\Http\Requests\AddOptionRequest;
use App\Http\Requests\UpdateOptionRequest;
use App\Http\Requests\DeleteOptionRequest;

use App\Services\ArrayBuilder\ArrayBuilderAssistant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OptionsController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Get all options
     * @param IndexOptionRequest $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return array
     */
    public function index(IndexOptionRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Option());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $query = $abAssistant->apply()->getQuery();

        if ($request['role'] === 'customer') $groups = OptionCategory::$customerGroups;
        if ($request['role'] === 'dealer') $groups = OptionCategory::$dealerGroups;
        if(!empty($groups))
            $query->whereHas('category', function($query) use($groups) {
                $query->whereIn('group', $groups);
            });

        $result = $abAssistant->setQuery($query)->paginate(
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
        $abAssistant->setModel(new Option());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) return response()->json($abAssistant->getMessages(), 400);

        $query = $abAssistant->apply()->getQuery();

        try {
            $item = $query->findOrFail($id);
            return response()->json($item);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['Option is not found.'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddOptionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddOptionRequest $request)
    {
        try
        {
            DB::transaction(function() use($request) {
                $optionParams = $request->all();
                $optionParams['taxable'] = $optionParams['taxable'] ?? null;
                $optionParams['rto'] = $optionParams['rto'] ?? null;

                $option = Option::create($optionParams);
                if ($option->id) {
                    if (array_key_exists('allowable_models_id', $optionParams)) {
                        $option->allowable_models()->sync((array) $optionParams['allowable_models_id']);
                    }

                    if (array_key_exists('allowable_colors_id', $optionParams)) {
                        $option->allowable_colors()->sync((array) $optionParams['allowable_colors_id']);
                    }

                    // add files
                    if ($request->file('upload_files')) {
                        $fileService = new FileService();
                        $fileService->store($request->file('upload_files'), [
                            'key' => $option->id,
                            'user_id' => Auth::check() ? Auth::user()->id : null,
                            'type' => 'option',
                            'id' => $option->id
                        ]);
                    }
                }
            });
            return response()->json(['Option successfully created.']);
        } catch (Exception $e)
        {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOptionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOptionRequest $request)
    {
        try
        {
            DB::transaction(function() use($request) {
                $option = Store::get('option');
                $optionParams = $request->all();
                $optionParams['taxable'] = !empty($optionParams['taxable']) ? "1" : null;
                $optionParams['rto'] = !empty($optionParams['rto']) ? "1" : null;

                $option->update($optionParams);
                if ($option->id) {
                    if (array_key_exists('allowable_models_id', $optionParams)) {
                        $option->allowable_models()->sync((array) $optionParams['allowable_models_id']);
                    }

                    if (array_key_exists('allowable_colors_id', $optionParams)) {
                        $option->allowable_colors()->sync((array) $optionParams['allowable_colors_id']);
                    }

                    // add files
                    if ($request->file('upload_files')) {
                        $fileService = new FileService();
                        $fileService->store($request->file('upload_files'), [
                            'key' => $option->id,
                            'user_id' => Auth::check() ? Auth::user()->id : null,
                            'type' => 'option',
                            'id' => $option->id
                        ]);
                    }
                }
            });

            return response()->json(['Option successfully updated.']);
        } catch (Exception $e)
        {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteOptionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteOptionRequest $request)
    {
        try
        {
            // get data which has got through validator
            $option = Store::get('option');
            $option->delete();
            return response()->json(['Option successfully deleted.']);
        } catch (Exception $e)
        {
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexOptionCategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function categories(IndexOptionCategoryRequest $request)
    {
        $role = $request->input('role');

        $groups = [];
        if ($role === 'customer') $groups = OptionCategory::$customerGroups;
        if ($role === 'dealer') $groups = OptionCategory::$dealerGroups;

        $optionCategories = OptionCategory::query();
        if (!empty($groups)) $optionCategories->whereIn('group', $groups);
        $optionCategories = $optionCategories->get();

        return response()->json($optionCategories);
    }

    /**
     * Get files specified option
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getFiles(Request $request)
    {
        $optionId = $request->route('option_id');
        $files = File::where('storable_type', '=', 'option')
            ->where('storable_id', '=', $optionId)->get();
        
        return response()->json($files);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeFlags(Request $request) {
        $isActiveFlags = Option::$isActive;

        return response()->json($isActiveFlags);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceQuantityFlags(Request $request) {
        $forceQuantity = Option::$forceQuantity;

        return response()->json($forceQuantity);
    }

    public function constraintTypes(Request $request) {
        $constraintType = Option::$constraintType;
        return response()->json($constraintType);
    }
}

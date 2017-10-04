<?php

namespace App\Http\Controllers\Api;

use DB;
use Event;
use Store;
use App\Models\Style;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddStyleRequest;
use App\Http\Requests\UpdateStyleRequest;
use App\Http\Requests\DeleteStyleRequest;
use App\Http\Controllers\Controller;

use App\Http\Requests\IndexStyleRequest;
use App\Services\ArrayBuilder\ArrayBuilderAssistant;

class StylesController extends Controller
{
    /**
     * Get all styles
     * @param IndexStyleRequest $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return array
     */
    public function index(IndexStyleRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new Style());
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
    public function show($id, Request $request, ArrayBuilderAssistant $abAssistant) {
        $abAssistant->setModel(new Style());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) return response()->json($abAssistant->getMessages(), 400);

        $query = $abAssistant->apply()->getQuery();

        try {
            $item = $query->findOrFail($id);
            return response()->json($item);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['Style is not found.'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddStyleRequest $request)
    {
        try
        {
            DB::transaction(function() use($request) {
                $styleParams = $request->all();

                if (isset($styleParams['short_code'])) {
                    $styleParams['short_code'] = strtoupper($styleParams['short_code']);
                }

                $style = Style::create($styleParams);
            });
            return response()->json(['Style successfully created.']);
        } catch (Exception $e)
        {
            Log::error($e);
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateStyleRequest $request)
    {
        try
        {
            DB::transaction(function() use($request, $id) {
                $style = Store::get('style');
                $styleParams = $request->all();

                if (isset($styleParams['short_code'])) {
                    $styleParams['short_code'] = strtoupper($styleParams['short_code']);
                }

                $style->update($styleParams);
            });

            return response()->json(['Style successfully updated.']);
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
    public function destroy(DeleteStyleRequest $request)
    {
        try
        {
            // get data which has got through validator
            $style = Store::get('style');
            $style->delete();
            return response()->json(['Style successfully deleted.']);
        } catch (Exception $e)
        {
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeFlags(Request $request) {
        $isActiveFlags = Style::$isActive;

        return response()->json($isActiveFlags);
    }
}

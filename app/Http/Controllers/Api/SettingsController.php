<?php

namespace App\Http\Controllers\Api;

use DB;
use Event;
use Store;
use App\Models\Setting;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\IndexSettingRequest;
use App\Http\Requests\AddSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Requests\DeleteSettingRequest;

use App\Http\Controllers\Controller;

use App\Services\ArrayBuilder\ArrayBuilderAssistant;

class SettingsController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Get all settings
     * @param IndexSettingRequest $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return array
     */
    public function index(IndexSettingRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        // $settings = Setting::get()->keyBy('id');

        // return response()->json($settings);

        $abAssistant->setModel(new Setting());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        // dd($abAssistant->apply());

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
     * @param ArrayBuilderAssistant $abAssistant
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request, ArrayBuilderAssistant $abAssistant) {
        $abAssistant->setModel(new Setting());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) return response()->json($abAssistant->getMessages(), 400);

        $query = $abAssistant->apply()->getQuery();

        try {
            $item = $query->findOrFail($id);
            return response()->json($item);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['Setting is not found.'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddSettingRequest $request)
    {
        try
        {
            DB::transaction(function() use($request) {
                $settingParams = $request->all();

                $setting = Setting::create($settingParams);
            });
            return response()->json(['Setting successfully created.']);
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
    public function update($id, UpdateSettingRequest $request)
    {
        try
        {
            DB::transaction(function() use($request) {
                $setting = Store::get('setting');
                $settingParams = $request->all();

                $setting->update($settingParams);
            });

            return response()->json(['Setting successfully updated.']);
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
    public function destroy(DeleteSettingRequest $request)
    {
        try
        {
            // get data which has got through validator
            $setting = Store::get('setting');
            $setting->delete();
            return response()->json(['Setting successfully deleted.']);
        } catch (Exception $e)
        {
            return response()->json(['Something went wrong.'], 422);
        }
    }

    /**
     * Temporarily dealer order form fallback :/
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function perId(Request $request)
    {
        $settings = Setting::get()->keyBy('id');

        return response()->json($settings);
    }
}

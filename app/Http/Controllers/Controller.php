<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getPerPageSetting()
    {
        return Setting::where('id', 'per_page')->first()->value;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function returnJsonErrorResponse()
    {
        return response()->json(['Something went wrong'], 422);
    }
}

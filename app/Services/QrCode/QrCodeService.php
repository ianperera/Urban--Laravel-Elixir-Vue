<?php

namespace App\Services\QrCode;

use App\Models\BuildingStatus;
use App\Models\File;
use App\Models\Location;

class QrCodeService
{

    /**
     * get images count.
     *
     * @param int $statusId
     * @return \Illuminate\Http\Response
     */

    public function getImagesCount(int $statusId)
    {
        $status = BuildingStatus::find($statusId);
        $next_priority = $status->priority + 1;
        $new_status_id = BuildingStatus::where('priority', $next_priority)->first();
        if ($new_status_id) {
            $new_status_id = $new_status_id->id;
        } else {
            $new_status_id = $statusId;
        }
        $category_id = $this->getStatusName($new_status_id);
        return File::where('category_id', $category_id)->count();
    }

    /**
     * get status name and convert spaces with _ .
     *
     * @param int $statusId
     * @return \Illuminate\Http\Response
     */
    public function getStatusName(int $statusId)
    {
        $status_name = BuildingStatus::find($statusId)->name;
        return preg_replace('/\s+/', '_', strtolower($status_name));
    }

    /**
     * get dealer locations
     *
     * @return \Illuminate\Http\Response
     */

    public function getdealerActiveLocation()
    {
        return Location::where('category', 'dealer')->where('is_active', 'yes')->get();
    }

    /**
     * return locations image count
     *
     * @param int $locationId
     * @return \Illuminate\Http\Response
     */
    public function getLocationImageCount(int $locationId)
    {
        return File::where('storable_id', $locationId)->where('storable_type', 'location')->count();

    }
}

<?php

namespace App\Services\Building;

use App\Models\Building;
use App\Models\BuildingHistory;
use App\Models\BuildingLocation;
use App\Models\BuildingStatus;
use App\Models\BuildingOptionColor;
use App\Models\LocationFiles;
use App\Models\File;
use App\Models\Location;

use App\Models\PriceGroup;
use App\Services\Files\FileService;
use App\Services\Building\BuildingStatusService;

use App\Events\BuildingWasAdded;
use App\Events\BuildingWasUpdated;

use DB;
use Event;
use Auth;
use Helper;

class BuildingService
{
    /**
     * save colors
     * @param $buildingOption
     * @param $color
     * @return mixed
     */
    public function saveColors($buildingOption, $color)
    {
        $cacheOptionGroups = ['roof', 'trim', 'siding'];
        // Add color
        $buildingOptionColor = BuildingOptionColor::firstOrNew(['building_option_id' => $buildingOption->id]);
        $buildingOptionColor->building_option_id = $buildingOption->id;
        $buildingOptionColor->color_id = $color['id'];

        if ($color['name']) {
            $buildingOptionColor->custom = $color['name'];
        }

        $buildingOptionColor->save();
        $group = $buildingOptionColor->building_option->option->category->group;
        $building = $buildingOptionColor->building_option->building;
        if (in_array($group, $cacheOptionGroups)) {
            if ($group === 'roof') {
                $building->roof_bo_id = $buildingOption->id;
            }
            elseif ($group === 'trim') {
                $building->trim_bo_id = $buildingOption->id;
            }
            elseif ($group === 'siding') {
                $building->siding_bo_id = $buildingOption->id;
            }
            $building->save();
        }
        return $this;
    }

    /**
     * save files
     * @param Building $building
     * @param array $buildingData
     * @param array $options
     * @return mixed
     */
    public function saveFiles(Building $building, array $buildingData = [], array $options = [])
    {
        // Add files
        if (isset($buildingData['files'])) {
            $fileService = new FileService();
            $fileService->store($buildingData['files'], [
                'key' => $building->serial_number,
                'user_id' => Auth::check() ? Auth::user()->id : null,
                'type' => 'building',
                'id' => $building->id,
                'category_id' => $buildingData['category_id'] ?? null,
                'qr_image_resize' => $qrCodeResize ?? null,
            ], $options);
        }
        return $this;
    }

    /**
     *save location files
     * @param $building
     * @param array $buildingData
     * @param array $options
     * @return mixed
     */
    public function saveLocationFiles(Building $building, array $buildingData = [], array $options = [])
    {
        // Add files
        if (isset($buildingData['files'])) {
            $fileService = new FileService();
            $fileService->store($buildingData['files'], [
                'key' => $building->serial_number,
                'user_id' => Auth::check() ? Auth::user()->id : null,
                'type' => 'location',
                'id' => $buildingData['location_id'],
                'category_id' => 'location_files'
            ], $options);

            $files = $fileService->files;

            $initialPreview = [];
            foreach ($files as $key => $file) {
                $initialPreview[] = "/storage/" . $file->path . '' . $file->name;
            }

            $initialPreviewConfig = [];
            foreach ($files as $key => $file) {
                $initialPreviewConfig[] = ['caption' => $file->name, 'width' => '120px', 'size' => $file->size, 'url' => '/api/files/' . $file->id, 'key' => $file->id];
            }

            if (isset($buildingData['payload']['preserve'])) {
                $preserve = json_decode($buildingData['payload']['preserve'], true);
                if (is_array($preserve) && $preserve != null) {
                    foreach ($fileService->files as $key => $file) {
                        $ids[] = $file->id;
                    }
                    foreach ($preserve as $key => $value) {
                        $preserve[] = $value['id'];
                    }
                    $all = array_merge($ids, $preserve);
                    $files = File::whereIn('id', $all)->get();
                }

            }
            $data = [];
            foreach ($files as $key => $file) {
                # code...
                $data[$key]['file_id'] = $file->id;
                $data[$key]['path'] = $this->checkLatLong($file);
            }

            return [
                'initialPreview' => $initialPreview,
                'initialPreviewConfig' => $initialPreviewConfig,
                'append' => true,
                'item' => [
                    'files' => $this->clubFileIDs($data),
                    'path' => $this->getOneLatLong($data),
                    'fileItem' => $files,
                ],
            ];
        }
        return $this;
    }

    /**
     * return one lat long in case if multiple
     * @param $data
     * @return mixed
     */
    protected function getOneLatLong($data)
    {
        foreach ($data as $key => $value) {
            if (isset($value['path']["lat"])) {
                return $value['path'];
            }
        }
    }

    /**
     * club file IDs
     * @param $data
     * @return mixed
     */
    protected function clubFileIDs($data)
    {
        foreach ($data as $key => $value) {
            $file[$key]["id"] = $value["file_id"];
        }
        return $file;
    }

    /**
     * check lat long of image
     * @param $file
     * @return mixed
     */
    protected function checkLatLong($file)
    {
        $file = storage_path('app/public' . $file->path . '' . $file->name);
        return $this->read_gps_location($file);
    }

    /**
     * get lat long of image
     * @param $file
     * @return mixed
     */
    protected function read_gps_location($file)
    {
        try {
            $ext_whitelist = ['jpg', 'jpeg', 'tiff'];

            // extension for this file name
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            // make sure we will be able to get the exif data
            if (!in_array($extension, $ext_whitelist)) {
                return false;
            } else {
                if (is_file($file)) {
                    $info = exif_read_data($file);
                    if (isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
                        isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
                        in_array($info['GPSLatitudeRef'], ['E', 'W', 'N', 'S']) && in_array($info['GPSLongitudeRef'], ['E', 'W', 'N', 'S'])) {

                        $GPSLatitudeRef = strtolower(trim($info['GPSLatitudeRef']));
                        $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

                        $lat_degrees_a = explode('/', $info['GPSLatitude'][0]);
                        $lat_minutes_a = explode('/', $info['GPSLatitude'][1]);
                        $lat_seconds_a = explode('/', $info['GPSLatitude'][2]);
                        $lng_degrees_a = explode('/', $info['GPSLongitude'][0]);
                        $lng_minutes_a = explode('/', $info['GPSLongitude'][1]);
                        $lng_seconds_a = explode('/', $info['GPSLongitude'][2]);

                        $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
                        $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
                        $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
                        $lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
                        $lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
                        $lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];

                        $lat = (float)$lat_degrees + ((($lat_minutes * 60) + ($lat_seconds)) / 3600);
                        $lng = (float)$lng_degrees + ((($lng_minutes * 60) + ($lng_seconds)) / 3600);

                        //If the latitude is South, make it negative.
                        //If the longitude is west, make it negative
                        $GPSLatitudeRef == 's' ? $lat *= -1 : '';
                        $GPSLongitudeRef == 'w' ? $lng *= -1 : '';

                        return [
                            'lat' => $lat,
                            'lng' => $lng,
                        ];
                    }
                }
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * update location files lat long
     * @param $data
     * @return mixed
     */
    public function updateLatLong($data)
    {
        if (count($data["files"])) {
            foreach ($data["files"] as $key => $value) {
                $locationfile = new LocationFiles;
                $locationfile->file_id = $value['id'];
                $locationfile->verified_lat = round($data['latitude'], 6);
                $locationfile->verified_long = round($data['longitude'], 6);
                $locationfile->save();

                if ($data['notes'] && $data['notes'] !== "null" && $data['notes'] !== "undefined") {
                    $location = Location::find($data['location_id']);
                    $location->notes = $data['notes'];
                    $location->save();
                }
            }
        }
        return $this;
    }

    /**
     * save options
     * @param $building
     * @param $buildingData
     * @return mixed
     */
    public function saveOptions($building, $buildingData)
    {
        // Add options
        if (array_key_exists('options', $buildingData)) {
            $building->building_options()->delete();
            foreach ((array)$buildingData['options'] as $key => $bOption) {
                $optionUnitPrice = $bOption['unit_price'];
                $optionQuantity = $bOption['quantity'];

                $buildingOption = $building->building_options()->create([
                    'option_id' => $bOption['option_id'],
                    'total_price' => $optionUnitPrice * $optionQuantity,
                    'unit_price' => $optionUnitPrice,
                    'quantity' => $optionQuantity,
                ]);

                if (!empty($bOption['color'])) {
                    $this->saveColors($buildingOption, $bOption['color']);
                }
            }
        }
        return $this;
    }

    /**
     * save building
     * @param Building $building
     * @param $buildingData
     * @param $options
     * @return mixed
     */
    public function save(Building $building, $buildingData = [], $options = [])
    {
        if ($building->id)
            return $this->update($building, $buildingData, $options);
        return $this->create($buildingData, $options);
    }

    /**
     * create building
     * @param $buildingData
     * @param $options
     * @return mixed
     */
    public function create($buildingData = [], $options = [])
    {
        Helper::load('Building');

        if (array_key_exists('update_serial_number', $options) && $options['update_serial_number'] === true) {
            $serialNumber = building_get_serial_number($buildingData);

            $buildingData['sort_id'] = substr($serialNumber, 12, 3) . substr($serialNumber, -2) . substr($serialNumber, -6, 4);
            $buildingData['manufacture_year'] = '20' . substr($serialNumber, -2);
            $buildingData['serial_number'] = $serialNumber;
        }

        if (array_key_exists('shell_price', $buildingData))
            $buildingData['total_price'] = $buildingData['shell_price'];

        if (!array_key_exists('plant_id', $buildingData))
            $buildingData['plant_id'] = 1; // default plant

        // below code is for getting published price group ids from price_groups table and store those ids into the respective fields.
        $priceGroup = new PriceGroup();
        $priceGroupIds = $priceGroup->where('status', 'published')->pluck('id', 'category')->toArray();
        if (!empty($priceGroupIds)) {
            $buildingData['models_price_group_id'] = !empty($priceGroupIds['models']) ? $priceGroupIds['models'] : '';
            $buildingData['options_price_group_id'] = !empty($priceGroupIds['options']) ? $priceGroupIds['options'] : '';
        }

        $building = new Building($buildingData);
        $building->save();

        $this->saveOptions($building, $buildingData);
        $this->saveFiles($building, $buildingData, $options);

        Event::fire(new BuildingWasAdded(
            $building,
            Auth::user(),
            isset($options['defaultStatus']) ? $options['defaultStatus'] : false
        ));
        Event::fire(new BuildingWasUpdated($building));

        return $building;
    }

    /**
     * Update building
     * @param $building
     * @param $buildingData
     * @param $options
     * @return mixed
     */
    public function update($building, $buildingData = [], $options = [])
    {
        Helper::load('Building');


        if (isset($buildingData['date_building_returned'])) {
            $buildingData['sold_status'] = null;
        }
        if (isset($buildingData['new_location_id'])) {
            $newLocation = BuildingLocation::create(['building_id' => $buildingData['id'], 'location_id' => $buildingData['new_location_id'], 'user_id' => Auth::user()->id]);
            $newLocationId = $newLocation->id;
            $buildingData['last_location_id'] = $newLocationId;
        }


        if (array_key_exists('update_serial_number', $options) && $options['update_serial_number'] === true) {
            $serialData['building_model_id'] = $building->building_model_id;
            $serialData['width'] = $building->width;
            $serialData['length'] = $building->length;
            $serialData['height'] = $building->height;
            $serialData['plant_id'] = $building->plant_id;
            $serialData = array_merge($serialData, $buildingData);
            $serialNumber = building_get_serial_number($serialData, $building->serial_number);
            $buildingData['serial_number'] = $serialNumber;
            $buildingData['sort_id'] = substr($serialNumber, 12, 3) . substr($serialNumber, -2) . substr($serialNumber, -6, 4);
            $buildingData['manufacture_year'] = '20' . substr($serialNumber, -2);
        }

        // add next status (by priority) to building
        if (array_key_exists('next_status', $options) && $options['next_status'] === true) {
            $buildingData['status_id'] = $this->addStatus($building, Auth::user());
        }

        $building->update($buildingData);

        $this->saveOptions($building, $buildingData);
        $this->saveFiles($building, $buildingData);

        Event::fire(new BuildingWasUpdated($building));

        return $building;
    }

    /**
     * Add new NEXT status by priority to building and re-index status_id
     * @param $building
     * @param $user
     * @param $contractor
     * @return mixed
     */
    public function addStatus(&$building, $user = null, $contractor = null)
    {
        $buildingStatus = BuildingStatus::where('default_for_sale', 1)->firstOrFail();

        $buildingHistory = new BuildingHistory([
            'building_id' => $building->id,
            'status_id' => $buildingStatus->id,
        ]);

        if ($user) $buildingHistory->user_id = $user->id;
        if ($contractor) $buildingHistory->contractor_id = $contractor->id;

        $buildingHistory = $building->building_history()->save($buildingHistory);
        return $buildingHistory->id;
    }

    /**
     * Update building location
     * @param $data
     * @return $this
     */
    public function updateBuildingLocation($data)
    {
        $building = Building::find($data['building_id']);
        $building_locations = BuildingLocation::find($building->last_location_id);
        if ($building_locations && $building_locations->location_id != $data['location_id']) {
            $building_location = new BuildingLocation;
            $building_location->user_id = \Auth::user()->id;
            $building_location->building_id = $building->id;
            $building_location->location_id = $data['location_id'];
            if ($building_location->save()) {
                $building->last_location_id = $building_location->id;
                $building->save();
            }
        }
        return $this;
    }
}
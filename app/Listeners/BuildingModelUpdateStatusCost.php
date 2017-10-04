<?php

namespace App\Listeners;

use App\Models\BuildingModel;
use App\Models\ModelBuildingStatus;
use App\Events\BuildingModelWasUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BuildingModelUpdateStatusCost
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param BuildingModelWasUpdated $event
     */
    public function handle(BuildingModelWasUpdated $event)
    {
        if (empty($event->data['building_model_status_cost'])) return;
        
        $buildingModel = $event->buildingModel;
        $buildingModelStatusCost = $event->data['building_model_status_cost'];

        // delete old rows
        $buildingModel->model_building_status()->forceDelete();
        foreach ( $buildingModelStatusCost as $status_id => $cost)
        {
            // skip empty, storing not needed
            if( empty($cost) ) continue;

            // add model building status
            $buildingModelStatus = new ModelBuildingStatus([
                'model_id' => $buildingModel->id,
                'status_id' => $status_id,
                'cost' => $cost,
            ]);

            $buildingModelStatus->save();
        }
    }
}

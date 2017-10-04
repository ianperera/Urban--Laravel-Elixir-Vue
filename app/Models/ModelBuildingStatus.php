<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelBuildingStatus extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'model_building_statuses';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['model_id', 'status_id', 'cost'];

    /**
     * A model building status belongs to one building model
     * @return \App\Models\BuildingModel
     */
    public function building_model()
    {
        return $this->belongsTo('App\Models\BuildingModel', 'id', 'model_id');
    }

    /**
     * A model building status belongs to one building status
     * @return \App\Models\BuildingStatus
     */
    public function building_status()
    {
        return $this->belongsTo('App\Models\BuildingStatus', 'id', 'status_id');
    }
}

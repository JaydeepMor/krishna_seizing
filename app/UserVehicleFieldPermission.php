<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class UserVehicleFieldPermission extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'vehicle_allowed_fields',
        'user_id'
    ];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'vehicle_allowed_fields' => ['required', 'string'],
            'user_id'                => ['required', 'integer', 'exists:' . User::getTableName() . ',id']
        ]);
    }

    public function city()
    {
        return $this->hasOne('App\City', 'id', 'city_id');
    }
}

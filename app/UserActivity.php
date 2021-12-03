<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use App\Vehicle;

class UserActivity extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'latitude',
        'longitude'
    ];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'user_id'    => ['required', 'integer', 'exists:' . User::getTableName() . ',id'],
            'vehicle_id' => ['required', 'integer', 'exists:' . Vehicle::getTableName() . ',id'],
            'latitude'   => ['required'],
            'longitude'  => ['required']
        ]);
    }

    public function validators(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            '*.user_id'    => ['required', 'integer', 'exists:' . User::getTableName() . ',id'],
            '*.vehicle_id' => ['required', 'integer', 'exists:' . Vehicle::getTableName() . ',id'],
            '*.latitude'   => ['required'],
            '*.longitude'  => ['required']
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}

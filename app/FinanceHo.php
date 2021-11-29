<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\City;

class FinanceHo extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'address',
        'vendor_code',
        'contact_number',
        'contact_person',
        'email',
        'gst_number',
        'city_id'
    ];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'name'    => ['required', 'string', 'max:255'],
            'city_id' => ['nullable', 'integer', 'exists:' . City::getTableName() . ',id'],
            'email'   => ['nullable', 'unique:' . $this->getTableName() . ',email,' . $id . ',id']
        ]);
    }

    public function city()
    {
        return $this->hasOne('App\City', 'id', 'city_id');
    }
}

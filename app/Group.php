<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\GroupFinance;

class Group extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'status'
    ];

    const STATUS_ACTIVE   = '1';
    const STATUS_INACTIVE = '0';
    public $statuses = [
        self::STATUS_ACTIVE   => "Active",
        self::STATUS_INACTIVE => "Inactive"
    ];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'name'   => ['required', 'string', 'max:255'],
            'status' => ['in:' . implode(",", array_keys($this->statuses))],
        ]);
    }

    public function groupFinances()
    {
        return $this->hasMany(GroupFinance::class);
    }
}

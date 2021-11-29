<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class UserSubscription extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'from',
        'to',
        'user_id'
    ];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'from'    => ['required'],
            'to'      => ['required'],
            'user_id' => ['required', 'integer', 'exists:' . User::getTableName() . ',id']
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

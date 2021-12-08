<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class WhatsappMessage extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'sid',
        'from',
        'to',
        'body',
        'user_id'
    ];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'sid'     => ['nullable', 'string', 'max:255'],
            'from'    => ['nullable', 'string', 'max:255'],
            'to'      => ['nullable', 'string', 'max:255'],
            'body'    => ['nullable', 'string', 'max:255'],
            'user_id' => ['required', 'integer', 'exists:' . User::getTableName() . ',id']
        ]);
    }
}

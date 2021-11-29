<?php

namespace App;

use Illuminate\Support\Facades\Validator;

class Constant extends BaseModel
{
    protected $fillable = [
        'key',
        'value'
    ];

    public function validator(array $data, $id = NULL)
    {
        return Validator::make($data, [
            'key'        => ['required', 'string', 'unique:' . $this->getTableName() . ',key,' . $id],
            'value'      => ['required', 'string']
        ]);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}

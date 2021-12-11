<?php

namespace App;

use Illuminate\Support\Facades\Validator;

class Constant extends BaseModel
{
    protected $fillable = [
        'key',
        'value'
    ];

    public $allowedAppExtensions = ['apk'];
    public $fileSystem           = 'public';
    public $appPath              = 'application';

    public function validator(array $data, $id = NULL)
    {
        if (!empty($data['key']) && $data['key'] == 'RELEASED_APPLICATION') {
            return $this->appValidator($data, $id);
        }

        return Validator::make($data, [
            'key'        => ['required', 'string', 'unique:' . $this->getTableName() . ',key,' . $id],
            'value'      => ['required', 'string']
        ]);
    }

    public function appValidator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'key'   => ['required', 'string', 'unique:' . $this->getTableName() . ',key,' . $id],
            'value' => ['required', 'regex:(^.+\.' . implode(",", $this->allowedAppExtensions) . '$)']
        ]);
    }
}

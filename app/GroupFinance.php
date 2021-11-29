<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Group;
use App\FinanceCompany;

class GroupFinance extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'group_id',
        'finance_company_id'
    ];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'group_id'             => ['required', 'integer', 'exists:' . Group::getTableName() . ',id'],
            'finance_company_id.*' => ['required', 'integer', 'exists:' . FinanceCompany::getTableName() . ',id'],
        ]);
    }
}

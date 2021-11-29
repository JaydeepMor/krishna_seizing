<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\FinanceHo;

class FinanceCompany extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'address',
        'contact_number',
        'contact_person',
        'branch_code',
        'branch_name',
        'email',
        'finance_ho_id'
    ];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'name'          => ['required', 'string', 'max:255'],
            'finance_ho_id' => ['nullable', 'integer', 'exists:' . FinanceHo::getTableName() . ',id'],
            'email'         => ['nullable', 'unique:' . $this->getTableName() . ',email,' . $id . ',id']
        ]);
    }

    public function financeHo()
    {
        return $this->hasOne('App\FinanceHo', 'id', 'finance_ho_id');
    }
}

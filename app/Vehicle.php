<?php

namespace App;

use App\User;
use Illuminate\Support\Facades\Validator;

class Vehicle extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loan_number',
        'customer_name',
        'model',
        'registration_number',
        'chassis_number',
        'engine_number',
        'arm_rrm',
        'mobile_number',
        'brm',
        'final_confirmation',
        'final_manager_name',
        'final_manager_mobile_number',
        'address',
        'branch',
        'bkt',
        'area',
        'region',
        'is_confirm',
        'is_cancel',
        'user_id',
        'lot_number'
    ];

    public $allowedExcelExtensions = ['xlsx', 'csv', 'xls'];
    public $fileSystem             = 'public';
    public $excelPath              = 'vehicle\\excel';

    const NOT_CONFIRM = '0';
    const CONFIRM     = '1';
    public $isConfirm = [
        self::NOT_CONFIRM => "Not Confirm",
        self::CONFIRM     => "Confirmed"
    ];

    const NOT_CANCEL = '0';
    const CANCEL     = '1';
    public $isCancel = [
        self::NOT_CANCEL => "Not Cancel",
        self::CANCEL     => "Cancelled"
    ];

    public function excelValidator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'excel_import' => ['required', 'mimes:' . implode(",", $this->allowedExcelExtensions)],
            'user_id'      => ['nullable', 'integer', 'exists:' . User::getTableName() . ',id']
        ]);
    }

    public static function getNextLotNumber()
    {
        $lastLotNumber = self::orderBy("id", "DESC")->first();

        return (!empty($lastLotNumber) ? ($lastLotNumber->lot_number + 1) : 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

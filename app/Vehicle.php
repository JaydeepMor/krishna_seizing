<?php

namespace App;

use App\User;
use App\FinanceCompany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

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
        'finance_company_id',
        'user_id',
        'lot_number'
    ];

    public $allowedExcelExtensions = ['xlsx'];
    public $fileSystem             = 'public';
    public $excelPath              = 'vehicle\\excel';
    public $sampleExcelFileName    = 'EASY-SIZING-VEHICLE-FORMET-SAMPLE.xlsx';
    public $fileHeadings           = [
        0 => "loan_number",
        1 => "customer_name",
        2 => "model",
        3 => "reg_no",
        4 => "chasis_no",
        5 => "engine_no",
        6 => "arm_rrm",
        7 => "mob_no",
        8 => "brm",
        9 => "final_confirmation",
        10 => "final_manager_name",
        11 => "final_manager_mob_no",
        12 => "add",
        13 => "branch",
        14 => "bkt",
        15 => "area",
        16 => "region"
    ];

    const EXCEL_EXPORT_PATH = 'storage/vehicle/excel/exported';

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

    const MAX_ALLOWED_FILE_SIZE = 4096;
    const MAX_IMPORTABLE_ROWS   = 50000;

    // Fixed now as we stored in Redis cache.
    const API_PAGINATION = 500;

    const VEHICLE_REDIS_KEY = 'vehicles:';

    public $appends = ['finance_company'];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'finance_company_id' => ['required', 'integer', 'exists:' . FinanceCompany::getTableName() . ',id'],
            'user_id'            => ['nullable', 'integer', 'exists:' . User::getTableName() . ',id']
        ]);
    }

    public function excelValidator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'excel_import'       => ['required', 'max:' . self::MAX_ALLOWED_FILE_SIZE, 'mimes:' . implode(",", $this->allowedExcelExtensions)],
            'finance_company_id' => ['required', 'integer', 'exists:' . FinanceCompany::getTableName() . ',id'],
            'user_id'            => ['nullable', 'integer', 'exists:' . User::getTableName() . ',id']
        ]);
    }

    public function excelHeadingsValidator(array $data)
    {
        if (!empty($data[0][0])) {
            if (count($this->fileHeadings) == count(array_filter($data[0][0], function($x) { return !empty($x); }))) {
                return ['code' => 200];
            }
        }

        return ['code' => 401, 'msg' => _("Excel is not valid. Check headings or download sample excel and check.")];
    }

    public function excelTotalRowsValidator(array $data)
    {
        if (!empty($data[0])) {
            if (count($data[0]) <= self::MAX_IMPORTABLE_ROWS) {
                return ['code' => 200];
            }
        }

        return ['code' => 401, 'msg' => _("Excel is out of data. Set less than " . self::MAX_IMPORTABLE_ROWS . " rows.")];
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

    public function financeCompany()
    {
        return $this->belongsTo(FinanceCompany::class);
    }

    public function getFinanceCompanyAttribute()
    {
        $financeCompany = $this->financeCompany()->first();

        return (!empty($financeCompany) && !empty($financeCompany->name)) ? $financeCompany->name : "";
    }

    public static function arrangeApiData(LengthAwarePaginator $vehicles)
    {
        $vehiclesData = collect();

        if (!empty($vehicles) && !$vehicles->isEmpty()) {
            foreach ($vehicles->toArray() as $field => &$value) {
                if (in_array($field, ['first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url', 'from', 'to'])) {
                    continue;
                }

                if ($field == 'data') {
                    if (!empty($value)) {
                        foreach ($value as $key => $row) {
                            unset($value[$key]['finance_company_id']);
                        }
                    }
                }

                $vehiclesData->put($field, $value);
            }
        }

        return $vehiclesData;
    }
}

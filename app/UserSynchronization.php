<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use App\User;
use App\FinanceCompany;

class UserSynchronization extends BaseModel
{
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'finance_company_id',
        'vehicle_count',
        'is_synced',
        'is_deleted'
    ];

    const IS_SYNCED_NOPE = '0';
    const IS_SYNCED_YES = '1';

    const IS_DELETED_NOPE = '0';
    const IS_DELETED_YES = '1';

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'user_id' => ['required', 'integer', 'exists:' . User::getTableName() . ',id'],
            'finance_company_id' => ['required', 'integer', 'exists:' . FinanceCompany::getTableName() . ',id'],
            'vehicle_count' => ['required', 'integer'],
            'is_synced' => ['required', 'in:0,1'],
            'is_deleted' => ['required', 'in:0,1']
        ]);
    }

    public static function setIsSynced(int $userId, $isSynced = self::IS_SYNCED_NOPE)
    {
        return self::where('user_id', $userId)->update(['is_synced' => $isSynced]);
    }

    public static function setIsSyncedByFinanceCompany(int $financeCompanyId, $isSynced = self::IS_SYNCED_NOPE, $isDeleted = self::IS_DELETED_NOPE)
    {
        $vehicleCount = Vehicle::where('finance_company_id', $financeCompanyId)->whereNotNull('registration_number')->where('registration_number', '!=', '')->count();

        return self::where('finance_company_id', $financeCompanyId)->update(['is_synced' => $isSynced, 'is_deleted' => $isDeleted, 'vehicle_count' => $vehicleCount]);
    }

    public static function setIsDeletedByFinanceCompany(int $financeCompanyId, $isDeleted = self::IS_DELETED_YES)
    {
        $vehicleCount = Vehicle::where('finance_company_id', $financeCompanyId)->whereNotNull('registration_number')->where('registration_number', '!=', '')->count();

        return self::where('finance_company_id', $financeCompanyId)->update(['is_deleted' => $isDeleted, 'vehicle_count' => $vehicleCount]);
    }
}

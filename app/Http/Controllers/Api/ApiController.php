<?php

namespace App\Http\Controllers\Api;

use App\Vehicle;
use App\UserVehicleFieldPermission;
use App\User;
use App\ApiKey;
use App\UserActivity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class ApiController extends BaseController
{
    public function getVehicles(Request $request)
    {
        $userId  = $request->get('user_id', NULL);

        $pageNo  = (int)$request->get('page_number', 1);

        $pageNo  = empty($pageNo) ? 1 : $pageNo;

        $perPage = (int)$request->get('per_page', 1000);

        // $perPage = empty($perPage) ? 1000 : $perPage;

        // Fixed now as we stored in Redis cache.
        $perPage = Vehicle::API_PAGINATION;

        $redis   = Redis::connection();

        // Get all vehicles from Redis cache first if not found than get from MySql.
        $vehiclesData = env('VEHICLE_API_CACHE', false) ? json_decode($redis->get(Vehicle::VEHICLE_REDIS_KEY . $pageNo . ":" . $perPage), true) : [];

        if (empty($vehiclesData) || empty($vehiclesData['data'])) {
            $vehicles     = Vehicle::select(['id', 'loan_number', 'customer_name', 'model', 'registration_number', 'chassis_number', 'engine_number', 'arm_rrm', 'mobile_number', 'brm', 'final_confirmation', 'final_manager_name', 'final_manager_mobile_number', 'address', 'branch', 'bkt', 'area', 'region', 'is_confirm', 'is_cancel', 'lot_number', 'finance_company_id'])->whereNotNull('registration_number')->where('registration_number', '!=', '')->paginate($perPage, ['*'], 'page', $pageNo);

            $vehiclesData = Vehicle::arrangeApiData($vehicles);
        } else {
            $vehiclesData['data'] = array_values($vehiclesData['data']);
        }

        // Get current user field permissions.
        /* $userFieldPermissions = UserVehicleFieldPermission::select('vehicle_allowed_fields')->where('user_id', $userId)->first();
        $vehicleAllowedFields = !empty($userFieldPermissions->vehicle_allowed_fields) ? json_decode($userFieldPermissions->vehicle_allowed_fields, true) : []; */

        // Get current user subscriptions.
        // $currentUser = User::find($userId);

        // Old Response as below,
        // , 'user_field_permissions' => $vehicleAllowedFields, 'user_subscriptions' => $currentUser->getCurrentSubscriptionTimestamps(), 'api_key' => $currentUser->getApiKey()
        return $this->returnSuccess(__('Records get successfully!'), ['vehicles' => $vehiclesData]);
    }

    public function userRegister(Request $request)
    {
        $modal = new User();

        $data  = $request->all();

        $validator = $modal->validator($data, NULL, true);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $create = $modal::create($data);

        if ($create) {
            $userVehicleFieldPermissionModal = new UserVehicleFieldPermission();
            $userVehicleFieldPermissionData  = [
                "vehicle_allowed_fields" => json_encode(json_decode(EXCEL_DATABASE_DEFAULT_FIELDS, true)),
                "user_id"                => $create->id
            ];
            $validator                       = $userVehicleFieldPermissionModal->validator($userVehicleFieldPermissionData);
            if (!$validator->fails()) {
                $userVehicleFieldPermissionModal::create($userVehicleFieldPermissionData);
            }

            ApiKey::generateKey($create->id, '0');

            return $this->getGlobalResponse($create->id, true);
        }

        return $this->returnError(__('Something went wrong. Please try again later or contact superadmin.'));
    }

    public function userActivity(Request $request)
    {
        $modal  = new UserActivity();

        $data   = $request->all();

        $userId = $request->get('user_id', NULL);

        $now    = Carbon::now();

        if (empty($data['data'])) {
            return $this->returnError(__('Provide at least one data to add.'));
        }

        $activityData = [];
        foreach ($data['data'] as $row) {
            // $rowArray               = json_decode($row, true);
            $rowArray               = $row;

            $rowArray['vehicle_id'] = empty($rowArray['vehicle_id']) ? NULL : $rowArray['vehicle_id'];
            $rowArray['latitude']   = empty($rowArray['latitude']) ? NULL : $rowArray['latitude'];
            $rowArray['longitude']  = empty($rowArray['longitude']) ? NULL : $rowArray['longitude'];
            $rowArray['user_id']    = $userId;
            $rowArray['created_at'] = $now;
            $rowArray['updated_at'] = $now;


            $activityData[]         = $rowArray;
        }

        $validator = $modal->validators($activityData, NULL, true);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $create = $modal::insert($activityData);

        if ($create) {
            return $this->returnSuccess(__('Record added successfully!'), User::getGlobalResponse($userId));
        }

        return $this->returnError(__('Something went wrong. Please try again later or contact superadmin.'));
    }

    public function getUserInfo(Request $request)
    {
        $apiKey     = (!empty($request->header('api-key'))) ? $request->header('api-key') : false;

        $getKeyInfo = $this->validateApiKey($apiKey);

        $userId     = 0;

        if (!empty($getKeyInfo)) {
            $userId = $getKeyInfo->user_id;
        }

        $imeiNo = $request->get('imei_number', NULL);

        if (!empty($imeiNo)) {
            return $this->getGlobalResponseImei($imeiNo);
        }

        return $this->getGlobalResponse($userId);
    }

    private function validateApiKey(string $key)
    {
        $getKeyInfo = ApiKey::where('key', $key)->first();

        return $getKeyInfo;
    }

    public function getGlobalResponse(int $userId, $isCreate = false)
    {
        $modal = new User();

        $user  = $modal::getGlobalResponse($userId);

        if (!empty($user)) {
            if ($isCreate) {
                return $this->returnSuccess(__('Record added successfully!'), $user);
            }

            return $this->returnSuccess(__('User information get successfully!'), $user);
        } else {
            if ($isCreate) {
                return $this->returnError(__('Record added successfully! But user not found.'), $this->noUserCode);
            }

            return $this->returnError(__('User not found.'), $this->noUserCode);
        }
    }

    public function getGlobalResponseImei($imeiNo)
    {
        $modal = new User();

        // Get User Id.
        $user = $modal::where('imei_number', $imeiNo)->first();

        if (!empty($user->id)) {
            return $this->getGlobalResponse($user->id);
        }

        return $this->returnError(__('User not found.'), $this->noUserCode);
    }
}

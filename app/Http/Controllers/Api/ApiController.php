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

class ApiController extends BaseController
{
    public function getVehicles(Request $request)
    {
        $userId = $request->get('user_id', NULL);

        $pageNo = $request->get('page_number', 1);

        // Get all vehicles.
        $vehiclesData = collect();
        $vehicles     = Vehicle::paginate(1000, ['*'], 'page', $pageNo);

        if (!empty($vehicles) && !$vehicles->isEmpty()) {
            foreach ($vehicles->toArray() as $field => &$value) {
                if (in_array($field, ['first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url', 'from', 'to'])) {
                    continue;
                }

                $vehiclesData->put($field, $value);
            }
        }

        // Get current user field permissions.
        $userFieldPermissions = UserVehicleFieldPermission::select('vehicle_allowed_fields')->where('user_id', $userId)->first();

        // Get current user subscriptions.
        $currentUser = User::find($userId);

        return $this->returnSuccess(__('Records get successfully!'), ['vehicles' => $vehiclesData, 'user_field_permissions' => $userFieldPermissions, 'user_subscriptions' => $currentUser->getCurrentSubscriptionTimestamps(), 'api_key' => $currentUser->getApiKey()]);
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
            $rowArray               = json_decode($row, true);

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
        $userId = $request->get('user_id', NULL);

        return $this->getGlobalResponse($userId);
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
                return $this->returnError(__('Record added successfully! But user not found.'));
            }

            return $this->returnError(__('User not found.'));
        }
    }
}

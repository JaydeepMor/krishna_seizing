<?php

namespace App\Http\Controllers\Api;

use App\Vehicle;
use App\UserVehicleFieldPermission;
use App\User;
use App\ApiKey;
use App\UserActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class ApiController extends BaseController
{
    public function getVehicles(Request $request)
    {
        $userId = $request->get('user_id', NULL);

        // Get all vehicles.
        $vehicles = Vehicle::all();

        // Get current user field permissions.
        $userFieldPermissions = UserVehicleFieldPermission::where('user_id', $userId)->get();

        // Get current user subscriptions.
        $currentUser = User::find($userId);

        return $this->returnSuccess(__('Records get successfully!'), ['vehicles' => $vehicles, 'user_field_permissions' => $userFieldPermissions, 'user_subscriptions' => $currentUser->getCurrentSubscription(), 'api_key' => $currentUser->getApiKey()]);
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

            $apiKey = ApiKey::generateKey($create->id);

            return $this->returnSuccess(__('Record added successfully!'), ['api_key' => $apiKey]);
        }

        return $this->returnError(__('Something went wrong. Please try again later or contact superadmin.'));
    }

    public function userActivity(Request $request)
    {
        $modal = new UserActivity();

        $data  = $request->all();

        $validator = $modal->validator($data, NULL, true);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $create = $modal::create($data);

        if ($create) {
            return $this->returnSuccess(__('Record added successfully!'), $create);
        }

        return $this->returnError(__('Something went wrong. Please try again later or contact superadmin.'));
    }
}
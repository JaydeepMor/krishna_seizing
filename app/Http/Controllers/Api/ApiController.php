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
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Notifications\CommonException;
use Notification;

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

            $count                = Vehicle::whereNotNull('registration_number')->where('registration_number', '!=', '')->count();

            $chunkSize            = Vehicle::API_PAGINATION;

            $lastPage             = (int)ceil($count / $chunkSize);

            $vehiclesData['last_page']  = $lastPage;
            $vehiclesData['total']      = $count;
        }

        // Get current user field permissions.
        /* $userFieldPermissions = UserVehicleFieldPermission::select('vehicle_allowed_fields')->where('user_id', $userId)->first();
        $vehicleAllowedFields = !empty($userFieldPermissions->vehicle_allowed_fields) ? json_decode($userFieldPermissions->vehicle_allowed_fields, true) : []; */

        // Get current user subscriptions.
        // $currentUser = User::find($userId);

        // Old Response as below,
        // , 'user_field_permissions' => $vehicleAllowedFields, 'user_subscriptions' => $currentUser->getCurrentSubscriptionTimestamps(), 'api_key' => $currentUser->getApiKey()
        // return $this->returnSuccess(__('Records get successfully!'), ['vehicles' => $vehiclesData]);
        return json_encode([
            'code' => $this->successCode,
            'msg'  => __('Records get successfully!'),
            'data' => ['vehicles' => $vehiclesData]
        ]);
    }

    public function userRegister(Request $request)
    {
        $modal = new User();

        $data  = $request->all();

        $validator = $modal->validator($data, NULL, true);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $idProofUpload = !empty($data['id_proof']) ? $data['id_proof'] : null;
        $selfieUpload  = !empty($data['selfie']) ? $data['selfie'] : null;

        unset($data['id_proof']);
        unset($data['selfie']);

        if (!empty($idProofUpload) && $idProofUpload instanceof UploadedFile) {
            $idProof   = $idProofUpload;

            $pathInfos = pathinfo($idProof->getClientOriginalName());

            if (!empty($pathInfos['extension'])) {
                $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . time() . '.' . $pathInfos['extension'];
                $fileName  = removeSpaces($fileName);

                $storeFile = $idProof->storeAs($modal->idProofPath, $fileName, $modal->fileSystem);

                if ($storeFile) {
                    $data['id_proof'] = $fileName;
                } else {
                    Notification::route('mail', config('mail.mine.email', 'it.jaydeep.mor@gmail.com'))->notify(new CommonException(__("User id proof not uploading from API. Store File : " . $storeFile)));

                    return $this->returnError(__('Something went wrong. Please try again later or contact superadmin.'));
                }
            }
        }

        if (!empty($selfieUpload) && $selfieUpload instanceof UploadedFile) {
            $selfie    = $selfieUpload;

            $pathInfos = pathinfo($selfie->getClientOriginalName());

            if (!empty($pathInfos['extension'])) {
                $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . time() . '.' . $pathInfos['extension'];
                $fileName  = removeSpaces($fileName);

                $storeFile = $selfie->storeAs($modal->selfiePath, $fileName, $modal->fileSystem);

                if ($storeFile) {
                    $data['selfie'] = $fileName;
                } else {
                    Notification::route('mail', config('mail.mine.email', 'it.jaydeep.mor@gmail.com'))->notify(new CommonException(__("User id proof not uploading from API. Store File : " . $storeFile)));

                    return $this->returnError(__('Something went wrong. Please try again later or contact superadmin.'));
                }
            }
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
            \Log::info('Activity error val: ' . $validator->errors()->first());
            return $this->returnError($validator->errors()->first());
        }

        $create = $modal::insert($activityData);

        \Log::info('Activity error create: ' . $create);

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

    public function getDemoVehicles(Request $request)
    {
        $modal    = new Vehicle();

        $contents = json_encode(["code" => 200, "msg" => "Records get successfully!", "data" => ["vehicles" => []]]);

        $filePath = 'vehicle/demoVehicles.json';

        $isExists = Storage::disk($modal->fileSystem)->exists($filePath);

        if ($isExists) {
            $contents = Storage::disk($modal->fileSystem)->get($filePath);

            if (!empty($contents)) {
                $contents = json_encode(json_decode($contents));
            }
        }

        return $contents;
    }

    public function userDownloadComplete(Request $request)
    {
        $userId = $request->get('user_id', null);

        if (!empty($userId)) {
            $isDone = User::changeIsDownloadable($userId, User::IS_DOWNLOADABLE_NO);

            if ($isDone) {
                return $this->getGlobalResponse($userId);
            }
        }

        return $this->returnError(__('User not found.'));
    }

    public function userDownloadIncomplete(Request $request)
    {
        $userId = $request->get('user_id', null);

        if (!empty($userId)) {
            $isDone = User::changeIsDownloadable($userId);

            if ($isDone) {
                return $this->getGlobalResponse($userId);
            }
        }

        return $this->returnError(__('User not found.'));
    }
}

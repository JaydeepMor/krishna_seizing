<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Group;
use App\UserSubscription;
use App\ApiKey;
use App\UserVehicleFieldPermission;
use App\Vehicle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'email',
        'contact_number',
        'team_leader',
        'imei_number',
        'status',
        'group_id',
        'id_proof',
        'selfie',
        'reference_name',
        'reference_mobile_number',
        'remember_token',
        'password',
        'is_admin',
        'is_downloadable'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    public $allowedImageExtensions = ['jpg', 'jpeg', 'png'];
    public $fileSystem             = 'public';
    public $idProofPath            = 'user\\id_proof';
    public $selfiePath             = 'user\\selfie';

    const ADMIN_ID = '1';

    const IS_USER   = '0';
    const IS_ADMIN  = '1';
    public $isAdmin = [
        self::IS_USER  => 'User',
        self::IS_ADMIN => 'Admin'
    ];

    const IS_DOWNLOADABLE_YES = '1';
    const IS_DOWNLOADABLE_NO  = '0';
    public $isDownloadable    = [
        self::IS_DOWNLOADABLE_YES => "Yes",
        self::IS_DOWNLOADABLE_NO => "Nope"
    ];

    const STATUS_ACTIVE   = '1';
    const STATUS_INACTIVE = '0';
    public $statuses      = [
        self::STATUS_ACTIVE   => "Active",
        self::STATUS_INACTIVE => "Inactive"
    ];

    public $appends = ['current_subscription', 'is_subscribed', 'api_key'];

    public function validator(array $data, int $id = NULL, $isApi = false)
    {
        $password = 'required';

        if (!empty($id) || $isApi) {
            $password = 'nullable';
        }

        $contact = ['nullable'];
        if ($isApi) {
            $contact = ['required', 'min:10', 'max:10'];
        }

        $allowedFields = ['required'];
        if ($isApi) {
            $allowedFields = ['nullable'];
        }

        return Validator::make($data, [
            'name'        => ['required', 'max:255'],
            'email'       => ['required', 'email', 'unique:' . $this->getTableName() . ',email,' . $id . ',id'],
            'imei_number' => ['required', 'max:255', 'unique:' . $this->getTableName() . ',imei_number,' . $id . ',id'],
            'status'      => ['in:' . implode(",", array_keys($this->statuses))],
            'group_id'    => ['nullable', 'integer', 'exists:' . Group::getTableName() . ',id'],
            'is_admin'    => ['in:' . implode(",", array_keys($this->isAdmin))],
            'is_downloadable' => ['in:' . implode(",", array_keys($this->isDownloadable))],
            'password'    => [$password, 'min:6', 'confirmed', 'required_with:password_confirmed'],
            'vehicle_allowed_fields' => $allowedFields,
            'contact_number' => $contact,
            'id_proof'    => ['nullable', 'mimes:' . implode(",", $this->allowedImageExtensions)],
            'selfie'      => ['nullable', 'mimes:' . implode(",", $this->allowedImageExtensions)],
            'reference_mobile_number' => ['nullable', 'min:10', 'max:10']
        ]);
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function getCurrentSubscription()
    {
        $userId = $this->id;

        $row    = UserSubscription::where('user_id', $userId)->orderBy('id', 'DESC')->first();

        if (!empty($row)) {
            return [
                'from' => date(DEFAULT_DATE_FORMAT, strtotime($row->from)),
                'to'   => date(DEFAULT_DATE_FORMAT, strtotime($row->to))
            ];
        } else {
            return [
                'from' => 0,
                'to'   => 0
            ];
        }
    }

    public function getCurrentSubscriptionAttribute()
    {
        return $this->getCurrentSubscription();
    }

    public function getCurrentSubscriptionTimestamps()
    {
        $subscriptions = $this->getCurrentSubscription();

        return [
            'from' => (!empty($subscriptions['from']) && strtotime($subscriptions['from']) > 0) ? (strtotime($subscriptions['from']) * 1000) : $subscriptions['from'],
            'to'   => (!empty($subscriptions['to']) && strtotime($subscriptions['to']) > 0) ? (strtotime($subscriptions['to']) * 1000) : $subscriptions['to']
        ];
    }

    public function getIsSubscribedAttribute()
    {
        $getCurrentSubscription = $this->getCurrentSubscription();

        if (!empty($getCurrentSubscription['to'])) {
            $now = strtotime(date(DEFAULT_DATE_FORMAT));

            if (strtotime($getCurrentSubscription['to']) > $now) {
                return true;
            }
        }

        return false;
    }

    public function getIdProofAttribute($value)
    {
        $storageFolderName = (str_ireplace("\\", "/", $this->idProofPath));

        if (!empty($value) && !empty($storageFolderName)) {
            $exists = Storage::disk($this->fileSystem)->exists($storageFolderName . '/' . $value);

            if ($exists) {
                $url = Storage::disk($this->fileSystem)->url($storageFolderName . '/' . $value);

                if (!empty($url)) {
                    $url = removeHttp($url);

                    return $url;
                }
            }
        }

        // return asset('img/placeholders/avatars/id-proof.png');
        return null;
    }

    public function getSelfieAttribute($value)
    {
        $storageFolderName = (str_ireplace("\\", "/", $this->selfiePath));

        if (!empty($value) && !empty($storageFolderName)) {
            $exists = Storage::disk($this->fileSystem)->exists($storageFolderName . '/' .  $value);

            if ($exists) {
                $url = Storage::disk($this->fileSystem)->url($storageFolderName . '/' . $value);

                if (!empty($url)) {
                    $url = removeHttp($url);

                    return $url;
                }
            }
        }

        // return asset('img/placeholders/avatars/selfie.png');
        return null;
    }

    public function getApiKey()
    {
        return ApiKey::getApiKey($this->id, false);
    }

    public function getApiKeyAttribute()
    {
        return $this->getApiKey();
    }

    public static function sortVehicleFields($unsortFields) {
        $newSortedFields = [];

        $sortedFields    = ["registration_number","chassis_number","loan_number","model","engine_number","bkt","arm_rrm","mobile_number","brm","branch","region","area","customer_name","final_confirmation","final_manager_name","final_manager_mobile_number","address","is_confirm","is_cancel","finance_company_id","user_id","lot_number"];

        if (!empty($unsortFields)) {
            $model  = new Vehicle();
            $fields = $model->getFillable();

            foreach ($fields as $field) {
                if (!in_array($field, $sortedFields)) {
                    $sortedFields[] = $field;
                }
            }

            foreach ($sortedFields as $sortedField) {
                if (in_array($sortedField, $unsortFields)) {
                    $newSortedFields[] = $sortedField;
                }
            }
        }

        if (empty($newSortedFields)) {
            $newSortedFields = $unsortFields;
        }

        return $newSortedFields;
    }

    public static function getGlobalResponse(int $userId)
    {
        $user = User::where('id', $userId)->where('is_admin', User::IS_USER)->first();

        if (!empty($user) && empty($user->api_key)) {
            // ApiKey::generateKey($userId);
        }

        if (!empty($user)) {
            $user->makeHidden('current_subscription');

            // Get current user field permissions.
            $userFieldPermissions         = UserVehicleFieldPermission::select('vehicle_allowed_fields')->where('user_id', $userId)->first();
            $user->user_field_permissions = !empty($userFieldPermissions->vehicle_allowed_fields) ? json_decode($userFieldPermissions->vehicle_allowed_fields, true) : [];

            // Sort fields.
            $user->user_field_permissions = self::sortVehicleFields($user->user_field_permissions);

            $user['user_subscriptions'] = $user->getCurrentSubscriptionTimestamps();

            // $user['total_vehicles'] = Vehicle::getCount();
            $totalVehicles = 0;

            if (env('VEHICLE_API_CACHE', false)) {
                $redis   = Redis::connection();
                $allKeys = $redis->keys(Vehicle::VEHICLE_REDIS_KEY . '*');
                
                if (!empty($allKeys)) {
                    foreach ($allKeys as $key) {
                        $vehiclesData = json_decode($redis->get($key), true);

                        if (!empty($vehiclesData['data']) && is_array($vehiclesData['data'])) {
                            $totalVehicles = $totalVehicles + count($vehiclesData['data']);
                        }
                    }
                }
            }

            if ($totalVehicles <= 0) {
                $totalVehicles = Vehicle::getCount();
            }

            $user['total_vehicles'] = $totalVehicles;
        }

        return $user;
    }

    public function routeNotificationForWhatsApp() {
        return $this->contact_number;
    }

    public function userSubscriptionsWithTrashed() {
        return $this->hasMany(UserSubscription::class)->withTrashed();
    }

    public function group() {
        return $this->hasOne('App\Group', 'id', 'group_id');
    }

    public function userVehicleFieldPermissions() {
        return $this->hasOne('App\UserVehicleFieldPermission', 'user_id', 'id');
    }

    public static function changeIsDownloadable(int $id, $flag = self::IS_DOWNLOADABLE_YES) {
        if (!in_array($flag, [self::IS_DOWNLOADABLE_YES, self::IS_DOWNLOADABLE_NO])) {
            return false;
        }

        return self::where('id', $id)->update(['is_downloadable' => $flag]);
    }

    public static function isDownloadableForAll() {
        return self::where('is_downloadable', self::IS_DOWNLOADABLE_NO)->update(['is_downloadable' => self::IS_DOWNLOADABLE_YES]);
    }
}

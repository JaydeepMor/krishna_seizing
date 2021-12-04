<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Group;
use App\UserSubscription;
use App\ApiKey;

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
        'remember_token',
        'password',
        'is_admin'
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

    const ADMIN_ID = '1';

    const IS_USER   = '0';
    const IS_ADMIN  = '1';
    public $isAdmin = [
        self::IS_USER  => 'User',
        self::IS_ADMIN => 'Admin'
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
            'email'       => ['required', 'unique:' . $this->getTableName() . ',email,' . $id . ',id'],
            'imei_number' => ['required', 'max:255'],
            'status'      => ['in:' . implode(",", array_keys($this->statuses))],
            'group_id'    => ['nullable', 'integer', 'exists:' . Group::getTableName() . ',id'],
            'is_admin'    => ['in:' . implode(",", array_keys($this->isAdmin))],
            'password'    => [$password, 'min:6', 'confirmed', 'required_with:password_confirmed'],
            'vehicle_allowed_fields' => $allowedFields,
            'contact_number' => $contact
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

    public function getApiKey()
    {
        return ApiKey::getAllApiKey($this->id);
    }

    public function getApiKeyAttribute()
    {
        return $this->getApiKey();
    }

    public static function getGlobalResponse(int $userId)
    {
        $user = User::where('id', $userId)->where('is_admin', User::IS_USER)->first();

        if (!empty($user) && empty($user->api_key)) {
            // ApiKey::generateKey($userId);
        }

        if (!empty($user)) {
            $user->makeHidden('current_subscription');

            $user['user_subscriptions'] = $user->getCurrentSubscriptionTimestamps();
        }

        return $user;
    }
}

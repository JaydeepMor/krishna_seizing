<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\User;

class ApiKey extends BaseModel
{
    protected $fillable = [
        'key',
        'is_valid',
        'user_id'
    ];

    public function validator(array $data, $id = false, $isUpdate = false)
    {
        if ($isUpdate === true && !empty($id)) {
            $keyValidator = ['unique:api_keys,key,' . $id];
        } else {
            $keyValidator = ['unique:api_keys'];
        }

        return Validator::make($data, [
            'key'      => array_merge(['required', 'string', 'max:255'], $keyValidator),
            'is_valid' => ['in:0,1'],
            'user_id'  => ['required', 'integer', 'exists:' . User::getTableName() . ',id']
        ]);
    }

    public static function generateKey(int $userId)
    {
        $key = md5(uniqid(rand(), true));

        // Check exists.
        if (self::where('user_id', $userId)->where('is_valid', '1')->exists()) {
            $record = self::where('user_id', $userId)->where('is_valid', '1')->first();
            $key    = $record->key;
        } else {
            self::create(['key' => $key, 'user_id' => $userId]);
        }

        return $key;
    }

    public static function getApiKey(int $userId)
    {
        $record = self::where('user_id', $userId)->where('is_valid', '1')->first();

        if (!empty($record)) {
            return $record->key;
        }

        return NULL;
    }

    public static function removeKey(int $userId)
    {
        $record = false;

        // Check exists.
        if (self::where('user_id', $userId)->where('is_valid', '1')->exists()) {
            $record = self::where('user_id', $userId)->where('is_valid', '1')->update(['is_valid' => '0']);
        }

        return $record;
    }

    /* For re-open removed key. */
    public static function appendKey(int $userId)
    {
        $key    = md5(uniqid(rand(), true));

        $record = false;

        // Check exists.
        if (self::where('user_id', $userId)->exists()) {
            $record = self::where('user_id', $userId)->update(['is_valid' => '1']);
        } else {
            self::create(['key' => $key, 'user_id' => $userId]);
        }

        return $record;
    }
}

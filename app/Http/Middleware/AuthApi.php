<?php

namespace App\Http\Middleware;

use App\Repositories\ApiKeyRepository;
use Closure;
use App\ApiKey;
use App\User;

class AuthApi
{
    private $excludedRoutes = [
        'api/user/register'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $apiKey = (!empty($request->header('api-key'))) ? $request->header('api-key') : false;

        if (in_array($request->route()->uri, $this->excludedRoutes)) {
            return $next($request);
        }

        $getKeyInfo = $this->validate($apiKey);

        if (!$apiKey || empty($getKeyInfo)) {
            return response()->json([
                'code' => 401,
                'msg'  => __('API key is missing or wrong.')
            ]);
        }

        if ($this->totalLogins($getKeyInfo->user_id)) {
            return response()->json([
                'code' => 401,
                'msg'  => __('You can\'t login on multiple device.')
            ]);
        }

        if (!$this->isSubscribed($getKeyInfo->user_id)) {
            return response()->json([
                'code' => 401,
                'msg'  => __('This user not subscribed or complete subscription. Please contact admin for a new subscription.')
            ]);
        }

        if ($request->has('user_id')) {
            $request->merge(['request_user_id' => $request->get('user_id')]);
        }

        $request->merge(['user_id' => $getKeyInfo->user_id]);

        return $next($request);
    }

    private function validate(string $key)
    {
        $getKeyInfo = ApiKey::where('key', $key)->where('is_valid', '1')->first();

        return $getKeyInfo;
    }

    private function totalLogins(int $userId)
    {
        $getUsers = ApiKey::where('user_id', $userId)->where('is_valid', '1')->count();

        return ($getUsers > config('app.allowed_api_user_logins'));
    }

    private function isSubscribed(int $userId):bool
    {
        $user = User::where('id', $userId)->where('is_admin', User::IS_USER)->first();

        return (!empty($user) && $user->is_subscribed);
    }
}

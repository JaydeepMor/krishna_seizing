<?php

namespace App\Http\Middleware;

use App\Repositories\ApiKeyRepository;
use Closure;
use App\ApiKey;
use App\User;

class AuthApi
{
    private $excludedRoutes = [
        'api/user/register',
        'api/user/info/get'
    ];

    private $allowedUnscribedRoutes = [
        'api/user/info/get'
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

        if ((!$apiKey || empty($getKeyInfo)) || (!$this->isSubscribed($getKeyInfo->user_id))) {
            return response()->json([
                'code' => 403,
                'msg'  => __('You are not subscribed yet or incomplete subscription. Please contact admin for subscription.')
            ]);
        }

        /*if (!$apiKey || empty($getKeyInfo)) {
            return response()->json([
                'code' => 403,
                'msg'  => __('API key is missing or wrong.')
            ]);
        }*/

        if ($this->totalLogins($getKeyInfo->user_id)) {
            return response()->json([
                'code' => 403,
                'msg'  => __('You can\'t login on multiple device.')
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
        if (in_array(request()->route()->uri, $this->allowedUnscribedRoutes)) {
            $getKeyInfo = ApiKey::where('key', $key)->first();
        } else {
            $getKeyInfo = ApiKey::where('key', $key)->where('is_valid', '1')->first();
        }

        return $getKeyInfo;
    }

    private function totalLogins(int $userId)
    {
        $getUsers = ApiKey::where('user_id', $userId)->where('is_valid', '1')->count();

        return ($getUsers > config('app.allowed_api_user_logins'));
    }

    private function isSubscribed(int $userId):bool
    {
        if (in_array(request()->route()->uri, $this->allowedUnscribedRoutes)) {
            return true;
        }

        $user = User::where('id', $userId)->where('is_admin', User::IS_USER)->first();

        return (!empty($user) && $user->is_subscribed);
    }
}

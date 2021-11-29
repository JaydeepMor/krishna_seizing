<?php

namespace App\Http\Middleware;

use App\Repositories\ApiKeyRepository;
use Closure;
use App\ApiKey;

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
}

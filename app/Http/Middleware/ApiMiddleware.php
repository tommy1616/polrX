<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ApiHelper;
use App\Exceptions\Api\ApiException;

class ApiMiddleware {
    protected static function getApiUserInfo(Request $request) {
        $api_key = $request->bearerToken()
            ?: $request->header('X-API-Key')
            ?: $request->header('X-API-KEY')
            ?: $request->input('key');
        $response_type = is_string($request->input('response_type')) ? $request->input('response_type') : 'json';

        if (!is_string($api_key) || $api_key === '') {
            // no API key provided; check whether anonymous API is enabled

            if (env('SETTING_ANON_API')) {
                $username = 'ANONIP-' . $request->ip();
            }
            else {
                throw new ApiException('AUTH_ERROR', 'Authentication token required.', 401, $response_type);
            }
            $user = (object) [
                'username' => $username,
                'anonymous' => true,
                'api_quota' => (int) env('SETTING_ANON_API_QUOTA', 10),
            ];
        }
        else {
            $user = User::where('active', 1)
                ->where('api_key', $api_key)
                ->where('api_active', 1)
                ->first();

            if (!$user || !hash_equals((string) $user->api_key, $api_key)) {
                throw new ApiException('AUTH_ERROR', 'Authentication token invalid.', 401, $response_type);
            }
            $username = $user->username;
            $user->anonymous = false;
        }

        $quota = isset($user->api_quota) ? (int) $user->api_quota : 60;
        if ($quota >= 0) {
            $maxPerMinute = max(1, $quota);
            $rateKey = 'api_quota:' . $username;
            if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateKey, $maxPerMinute)) {
                throw new ApiException('QUOTA_EXCEEDED', 'Quota exceeded.', 429, $response_type);
            }
            \Illuminate\Support\Facades\RateLimiter::hit($rateKey, 60);
        }

        $api_limit_reached = ApiHelper::checkUserApiQuota($username);

        if ($api_limit_reached) {
            throw new ApiException('QUOTA_EXCEEDED', 'Quota exceeded.', 429, $response_type);
        }
        return $user;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next) {
        $user = $this->getApiUserInfo($request);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->attributes->set('user', $user);

        return $next($request);
    }
}

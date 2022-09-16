<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Response;
use Firebase\JWT\JWT;
use App\Models\Organization;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->header('Authorization') && strpos($request->header('Authorization'), 'JWT') !== false) {
            try {
                preg_match('/JWT\s((.*)\.(.*)\.(.*))/', $request->header('Authorization'), $jwt);
                $payload = JWT::decode($jwt[1], file_get_contents(config('jwt.keys.public')), [config('jwt.algo')]);
                if ($payload->organization_id) {
                    $organization = Organization::find($payload->organization_id);
                    if ($organization) {
                        auth('api')->setUser($user);
                    }
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => [
                        'message' => 'Unauthorized',
                        'code' => 40101, // Expired token
                        'status_code' => Response::HTTP_UNAUTHORIZED,
                    ],
                ], Response::HTTP_UNAUTHORIZED);
            }
            
        } 
        
        if ($this->auth->guard($guard)->guest()) {
            return response()->json([
                'error' => [
                    'message' => 'Unauthorized',
                    'code' => 40102, // Not login
                    'status_code' => Response::HTTP_UNAUTHORIZED,
                ],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}

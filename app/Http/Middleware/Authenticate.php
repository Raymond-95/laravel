<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
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

        if ($request->is('api/*')) {

            if (Auth::guard($guard)->guest()) {           
                return response()
                    ->api([], 'Unauthorized', 'Access is denied due to invalid credentials.', 401)
                ;                
            } 

        } else {

            if (Auth::guard($guard)->guest()) {
                if ($request->ajax()) {
                    return response('Unauthorized.', 401);
                } else {
                    return redirect()->guest('accounts/login');
                }
            }
            
        }

        return $next($request);
    }
}

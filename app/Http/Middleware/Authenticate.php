<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{

    protected function authenticate($request, array $guards)
    {
        if ($request->has('api_token')) {
            $api_token = $request->input('api_token');
            $request->headers->set('Authorization', "Bearer $api_token");
        }

        if (empty($guards)) {
            $guards = [null];
        }


        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        $this->unauthenticated($request, $guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            abort(response('Unauthorized', 401));
        }
    }
}

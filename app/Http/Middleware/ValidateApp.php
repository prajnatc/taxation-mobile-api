<?php

namespace App\Http\Middleware;

use Closure;

class ValidateApp
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

      if($request->header('api_key')===env('MOBILE_API_KEY')){
        return $next($request);
      }else{
        return response()->json([
            'message' => 'API Key failed',
        ], 401);

      }

    }
}

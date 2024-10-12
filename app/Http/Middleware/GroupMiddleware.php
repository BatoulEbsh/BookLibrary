<?php

namespace App\Http\Middleware;

use App\Models\GroupUser;
use App\Traits\ReturnResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupMiddleware
{
    use ReturnResponse;
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $groupUser = new GroupUser();
        if ($groupUser->group()!=$request['group_id']){
            return $this->returnError(404,'you are not in group');
        }
        return $next($request);
    }
}

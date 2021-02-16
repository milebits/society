<?php


namespace Milebits\Society\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Milebits\Society\Concerns\Sociable;

class IsAFriendOfMiddleware
{
    public function handle(Request $request, Closure $next, Model $person)
    {
        if (!$this->user($request)->society()->friends()->isFriendOf($person))
            return back();
        return $next($request);
    }

    /**
     * @param Request $request
     * @return mixed|Sociable
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}
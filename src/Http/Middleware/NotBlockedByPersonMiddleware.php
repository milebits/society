<?php

namespace Milebits\Society\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Milebits\Society\Concerns\Sociable;
use function hasTrait;

class NotBlockedByPersonMiddleware
{
    /**
     * @param Request $request
     * @param callable $next
     * @param Model|Sociable $person
     * @return RedirectResponse|Response
     */
    public function handle(Request $request, callable $next, $person)
    {
        if (!hasTrait($this->user($request)->getMorphClass(), Sociable::class))
            return $next($request);

        if (!$this->user($request)->society()->friends()->isBlockedBy($person)) return back();
        return $next($request);
    }

    /**
     * @param Request $request
     * @return Sociable|Model|mixed
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}
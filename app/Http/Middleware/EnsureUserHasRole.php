<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        $slugs = array_filter(explode('|', $roles));

        if ($slugs === [] || ! $user->hasAnyRole($slugs)) {
            abort(403);
        }

        return $next($request);
    }
}

<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class CheckPermission {
    public function handle(Request $request, Closure $next, string $permission): Response {
        if (!$request->user()) return redirect()->route('login');
        if (!$request->user()->canAccess($permission)) {
            return redirect()->back()->with('error','Anda tidak memiliki akses ke menu ini.');
        }
        return $next($request);
    }
}

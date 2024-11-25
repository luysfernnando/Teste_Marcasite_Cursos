<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $type = null)
    {

        $userType = Auth::user()->type === 0 ? 'admin' : 'student';

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($userType !== $type) {
            \Log::info('Usuário não tem permissão para acessar essa página.');
            return redirect('/')->with('error', 'Você não tem permissão para acessar essa página.');
        }

        return $next($request);
    }
}

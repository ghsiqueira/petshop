<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // Permitir que admin acesse qualquer rota protegida por papel
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return $next($request);
        }

        // Para outros usuários, verificar o papel específico
        if (!auth()->check() || !auth()->user()->hasRole($role)) {
            abort(403, 'Acesso não autorizado');
        }

        return $next($request);
    }
}
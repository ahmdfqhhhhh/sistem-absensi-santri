<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Jika belum login, alihkan ke halaman login
        if (! $user) {
            return redirect()->guest(route('login'))->with('error', 'Silakan masuk terlebih dahulu untuk mengakses menu ini.');
        }

        // 2. Super Admin memiliki hak akses bypass ke seluruh modul
        if ($user->role === User::ROLE_SUPER_ADMIN) {
            return $next($request);
        }

        // 3. Periksa apakah role user cocok dengan salah satu role yang diizinkan
        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        // 4. Jika role tidak berhak, kembalikan response atau redirect ke menu utama masing-masing
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengakses sumber daya ini.',
            ], Response::HTTP_FORBIDDEN);
        }

        // Arahkan ke rute default sesuai peran pengguna
        $redirectRoute = match ($user->role) {
            User::ROLE_KEAMANAN => route('kamtib.index'),
            User::ROLE_WALI_SANTRI => route('wali.portal'),
            User::ROLE_BENDAHARA => route('bendahara.index'),
            User::ROLE_MUSYRIF => route('home'),
            default => route('home'),
        };

        return redirect($redirectRoute)->with('error', 'Akses dibatasi. Halaman tersebut khusus untuk peran: '.implode(', ', array_map('ucfirst', $roles)).'.');
    }
}

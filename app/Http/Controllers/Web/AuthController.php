<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function loginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Memproses autentikasi kredensial pengguna.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            return $this->redirectBasedOnRole($user)
                ->with('success', "Selamat datang kembali, {$user->name}! Masuk sebagai peran ".ucfirst(str_replace('_', ' ', $user->role)).'.');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan form pendaftaran akun untuk Wali Santri.
     */
    public function registerWaliForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register_wali');
    }

    /**
     * Memproses pendaftaran akun Wali Santri baru.
     */
    public function registerWali(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'max:25'],
            'nis_santri' => ['required', 'string', 'exists:santris,nis'],
            'hubungan' => ['required', 'string', 'in:Ayah Kandung,Ibu Kandung,Wali / Saudara'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wali santri wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan login menggunakan akun Anda.',
            'no_hp.required' => 'Nomor WhatsApp / HP aktif wajib diisi.',
            'nis_santri.exists' => 'Santri dengan NIS tersebut tidak ditemukan di sistem. Mohon periksa kembali NIS ananda Anda.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $santri = Santri::where('nis', $validated['nis_santri'])->firstOrFail();

        // Hubungkan data nomor WA wali dan nama wali pada profil santri
        $santri->update([
            'no_wa_wali' => $validated['no_hp'],
            'nama_wali' => $validated['name'],
        ]);

        // Buat akun pengguna baru dengan peran wali_santri
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_WALI_SANTRI,
            'no_hp' => $validated['no_hp'],
            'gender' => 'semua',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('wali.portal')
            ->with('success', "Pendaftaran berhasil! Selamat datang, {$user->name}. Akun Anda telah terhubung dengan ananda {$santri->nama} (NIS: {$santri->nis}).");
    }

    /**
     * Fitur Masuk Cepat (1-Click Demo Login) sesuai 4 peran di PRD untuk kemudahan pengujian.
     */
    public function quickLogin(string $role): RedirectResponse
    {
        $validRoles = [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_MUSYRIF,
            User::ROLE_KEAMANAN,
            User::ROLE_WALI_SANTRI,
            User::ROLE_BENDAHARA,
        ];

        if (! in_array($role, $validRoles, true)) {
            return redirect()->route('login')->with('error', 'Peran tidak dikenali.');
        }

        // Cari user dengan role tersebut, atau buat jika belum ada
        $user = User::where('role', $role)->first();

        if (! $user) {
            $defaultName = match ($role) {
                User::ROLE_SUPER_ADMIN => 'Super Admin',
                User::ROLE_MUSYRIF => 'Ustadz Musyrif',
                User::ROLE_KEAMANAN => 'Petugas Keamanan',
                User::ROLE_WALI_SANTRI => 'Wali Santri',
                User::ROLE_BENDAHARA => 'Bendahara Pondok',
                default => ucfirst(str_replace('_', ' ', $role)),
            };

            $user = User::factory()->create([
                'name' => $defaultName,
                'email' => "{$role}@pesantren.test",
                'role' => $role,
            ]);
        }

        // Jika masuk sebagai Wali Santri dan belum terhubung ke santri manapun, kaitkan dengan santri aktif yang ada
        if ($role === User::ROLE_WALI_SANTRI) {
            $hasMatchingSantri = $user->no_hp && Santri::where('no_wa_wali', $user->no_hp)->exists();
            if (! $hasMatchingSantri) {
                $firstSantri = Santri::where('status', 'aktif')->whereNotNull('no_wa_wali')->first() ?? Santri::first();
                if ($firstSantri && $firstSantri->no_wa_wali) {
                    $user->update(['no_hp' => $firstSantri->no_wa_wali]);
                    $user->refresh();
                }
            }
        }

        Auth::login($user);
        request()->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Berhasil masuk sebagai [ '.strtoupper(str_replace('_', ' ', $role))." ] - {$user->name}.");
    }

    /**
     * Logout pengguna dan invalidate session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }

    /**
     * Redirect pengguna ke halaman utama sesuai peran masing-masing (SRS 2.2).
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            User::ROLE_KEAMANAN => redirect()->route('kamtib.index'),
            User::ROLE_WALI_SANTRI => redirect()->route('wali.portal'),
            User::ROLE_BENDAHARA => redirect()->route('bendahara.index'),
            User::ROLE_MUSYRIF => redirect()->route('home'),
            User::ROLE_SUPER_ADMIN => redirect()->route('home'),
            default => redirect()->route('home'),
        };
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CtUserHash;
use App\Models\Hp;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('lembur')->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'npk' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('npk', 'password');

        if (!Auth::guard('lembur')->attempt($credentials)) {

            return redirect()->route('login')->with('error', 'NPK atau password salah');
            // return back()->withErrors(['npk' => 'NPK atau password salah'])->withInput();
        }

        $user = Auth::guard('lembur')->user();

        Auth::guard('lembur')->logout();

        $hpRecord = Hp::where('npk', $user->npk)->first();

        if (!$hpRecord) {
            Auth::guard('lembur')->logout();

            return redirect()->route('login')->with('error', 'NPK tidak memiliki nomor HP terdaftar');
            // return back()->withErrors(['npk' => 'NPK tidak memiliki nomor HP terdaftar. Hubungi admin.'])->withInput();
        }

        Otp::where('user_id', $user->id)->where('is_used', false)->delete();

        $otp = Otp::create([
            'user_id' => $user->id,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(5),
        ]);

        session([
            'pending_user_id' => $user->id,
            'otp_id' => $otp->id,
            'otp_attempts' => 0,
            'auth_guard' => 'lembur',
        ]);

        return redirect()->route('otp.verify');

    }

    public function showOtpForm()
    {
        if (!session('pending_user_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        Log::info('Showing OTP form', [
            'session_id' => session()->getId(),
            'pending_user_id' => session('pending_user_id')
        ]);

        return view('auth.otp-verify');
    }

    public function verifyOtp(Request $request)
    {
        Log::info('OTP Verification attempt', [
            'session_id' => session()->getId(),
            'otp_from_request' => $request->otp,
            'pending_user_id' => session('pending_user_id'),
            'otp_id' => session('otp_id')
        ]);

        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('pending_user_id');
        $otpId = session('otp_id');
        $guard = session('auth_guard', 'lembur');

        if (!$userId || !$otpId) {
            return redirect()->route('login')->with('error', 'Sesi telah berakhir');
        }

        $otp = Otp::where('id', session('otp_id'))
            ->where('user_id', $userId)
            ->where('otp_code', $request->otp)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            $attempts = session('otp_attempts', 0) + 1;
            session(['otp_attempts' => $attempts]);

            if ($attempts >= 3) {
                session()->forget(['pending_user_id', 'otp_id', 'otp_attempts']);
                return redirect()->route('login')->with('error', 'Terlalu banyak percobaan. Silakan login ulang.');
            }

            return back()->with('error', 'Kode OTP tidak valid atau telah kadaluarsa');
        }

        $otp->update(['is_used' => true]);

        switch ($guard) {
            case 'lembur':
                $user = CtUserHash::find($userId);
                break;
            default:
                $user = User::find($userId);
                break;
        }

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan');
        }

        Auth::guard($guard)->login($user);

        $request->session()->regenerate();

        session()->forget(['pending_user_id', 'otp_id', 'otp_attempts', 'auth_guard']);

        Log::info('OTP verified successfully', [
            'user_id' => $user->id,
            'new_session_id' => session()->getId()
        ]);

        return redirect()->intended('/dashboard');
    }

    public function resendOtp()
    {
        $userId = session('pending_user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        Otp::where('user_id', $userId)
            ->where('is_used', false)
            ->delete();

        $otp = Otp::create([
            'user_id' => $userId,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(5),
        ]);

        session(['otp_id' => $otp->id]);
        session()->save();

        Log::info('OTP resent', [
            'user_id' => $userId,
            'new_otp_id' => $otp->id
        ]);

        return back()->with('success', 'Kode OTP baru telah dikirim');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();

        Auth::logout();

        if (config('session.driver') === 'database' && $userId) {
            DB::table('sessions')
                ->where('user_id', $userId)
                ->delete();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

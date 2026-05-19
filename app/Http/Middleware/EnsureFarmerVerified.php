<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureFarmerVerified
{
    /**
     * Ensure the authenticated farmer has a verified status
     * before accessing critical features (listings, orders management).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user && $user->isFarmer() && $user->verification_status !== 'verified') {
            $message = match ($user->verification_status) {
                'pending' => 'Akun Anda sedang dalam proses verifikasi. Harap tunggu persetujuan admin.',
                'rejected' => 'Verifikasi Anda ditolak. Silakan periksa catatan penolakan dan kirim ulang dokumen di halaman profil.',
                default => 'Anda harus menyelesaikan verifikasi terlebih dahulu untuk mengakses fitur ini. Silakan unggah dokumen di halaman profil.',
            };
            return redirect()->route('farmer.profile.edit')
                ->with('verification_warning', $message);
        }
        return $next($request);
    }
}

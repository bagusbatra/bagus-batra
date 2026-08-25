<?php

namespace App\Http\Middleware;

use App\Models\DisplaySetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Iterasi 22 (Fase 4) — mode maintenance. Lihat
 * docs/RENCANA-KUSTOMISASI-TAMPILAN.md bagian 3 baris "Mode maintenance" &
 * bagian 5 Iterasi 22.
 *
 * Didaftarkan SETELAH App\Http\Middleware\HandleAppearancePreview di grup
 * middleware "web" (bootstrap/app.php) — berlaku utk SEMUA route publik
 * (routes/web.php), TERMASUK routes/admin.php karena keduanya sama-sama
 * masuk lewat withRouting(web: ...).
 *
 * Dua jalur bypass, dicek SEBELUM membaca setting sama sekali:
 * 1. Path `/admin` atau `/admin/*` — dicek lewat PATH, bukan status login,
 *    supaya admin yang BELUM login pun tetap bisa membuka /admin/login
 *    untuk masuk lalu mematikan maintenance dari sana.
 * 2. Guard "web" sedang login (admin) — berlaku di rute PUBLIK manapun,
 *    supaya admin bisa mengecek tampilan situs publik apa adanya tanpa
 *    perlu logout dulu (sesuai keputusan di rencana).
 *
 * Nilai `maintenance_mode`/`maintenance_message_*` SELALU dibaca dengan
 * preview=false eksplisit (BUKAN request attribute `appearance_preview`) —
 * baris kode ini hanya pernah dieksekusi setelah kedua bypass di atas gagal,
 * artinya request ini PASTI dari pengunjung yang TIDAK login, yang menurut
 * App\Http\Middleware\HandleAppearancePreview tidak akan pernah punya mode
 * preview aktif apa pun isi query string/session-nya. Draft TIDAK PERNAH
 * bocor lewat jalur ini.
 */
class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        if (auth()->guard('web')->check()) {
            return $next($request);
        }

        if (! DisplaySetting::getBool('maintenance_mode', false, false)) {
            return $next($request);
        }

        $messageId = DisplaySetting::get('maintenance_message_id', null, false);
        $messageEn = DisplaySetting::get('maintenance_message_en', null, false);

        return response()->view('maintenance', compact('messageId', 'messageEn'), 503);
    }
}

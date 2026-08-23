<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteProfile extends Model
{
    protected $fillable = [
        'name',
        'nickname',
        'title_id',
        'title_en',
        'tagline_id',
        'tagline_en',
        'bio_id',
        'bio_en',
        'location',
        'email',
        'phone',
        'github',
        'linkedin',
        'twitter',
        'available_for_work',
        'years_of_exp',
        'completed_projects',
        'client_satisfaction',
        'open_source_contributions',
        'avatar',
        'secondary_avatar',
    ];

    protected $casts = [
        'available_for_work' => 'boolean',
    ];

    /**
     * Cache key used by current() below — also referenced by
     * Admin\ProfileController@update to invalidate after a save (Iterasi 15,
     * lihat docs/RENCANA-OPTIMASI-PERFORMA.md Iterasi 15).
     */
    public const CACHE_KEY = 'site_profile';

    /**
     * Singleton accessor — always returns (and creates if missing) the
     * single profile row with id = 1.
     *
     * Iterasi 15 (Fase 3): dibungkus Cache::remember karena data ini hanya
     * pernah berubah lewat Admin\ProfileController@update, yang memanggil
     * Cache::forget(self::CACHE_KEY) setelah setiap save berhasil — jadi
     * invalidasi selalu tepat waktu. TTL 1 jam tetap dipasang sebagai
     * jaring pengaman tambahan (bukan andalan utama) kalau-kalau ada jalur
     * penulisan lain yang lolos dari review di masa depan (mis. tinker,
     * seeder ulang tanpa lewat controller) — lihat keputusan lengkap di
     * docs/LOG-ITERASI.md entri Iterasi 15.
     *
     * PENTING: hanya array attribute mentah (bukan objek Model) yang
     * disimpan di cache. Project ini pakai `config('cache.serializable_classes')
     * = false` (default keamanan Laravel terbaru, mencegah gadget-chain
     * attack lewat cache yang di-unserialize) — meng-cache objek Eloquent
     * langsung akan diam-diam kembali sebagai `__PHP_Incomplete_Class` saat
     * dibaca ulang (dikonfirmasi lewat percobaan nyata saat implementasi).
     * Solusinya: cache `getAttributes()` (array biasa, aman di-unserialize),
     * lalu rehydrate ke instance Model lewat `newFromBuilder()` — pola yang
     * sama dipakai Eloquent sendiri saat hydrate hasil query, jadi hasilnya
     * identik dengan instance dari `firstOrCreate()` (exists=true, cast
     * jalan normal), hanya tanpa query database saat cache HIT.
     */
    public static function current(): self
    {
        $attributes = Cache::remember(self::CACHE_KEY, 3600, function () {
            return static::firstOrCreate(['id' => 1])->getAttributes();
        });

        return (new static)->newFromBuilder($attributes);
    }
}

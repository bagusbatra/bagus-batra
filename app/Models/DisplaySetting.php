<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pengaturan tampilan generik (key-value) — Iterasi 18, Fase 4. Lihat
 * docs/RENCANA-KUSTOMISASI-TAMPILAN.md bagian 3 & 5.
 *
 * `value` = nilai LIVE, dibaca visitor publik biasa. `value_draft` = nilai
 * draft pending (NULL berarti tidak ada perubahan pending). Nilai disimpan
 * sebagai TEXT mentah (string) — untuk boolean dipakai konvensi string
 * '1'/'0' (bukan 'true'/'false') supaya cast native PHP `(bool) $value`
 * berperilaku benar apa adanya (PHP secara khusus menganggap string "0"
 * sebagai falsy, jadi (bool)'0' === false, (bool)'1' === true — tidak perlu
 * parsing tambahan di call site).
 */
class DisplaySetting extends Model
{
    protected $fillable = [
        'setting_key',
        'value',
        'value_draft',
    ];

    /**
     * Ambil nilai efektif satu setting.
     *
     * $preview = false (default, dipakai untuk visitor publik biasa) SELALU
     * mengembalikan `value` live — draft TIDAK PERNAH bocor lewat jalur ini
     * apa pun kondisinya.
     *
     * $preview = true (dipakai admin yang login + mode preview aktif)
     * mengembalikan value_draft ?? value ?? $default.
     */
    public static function get(string $key, mixed $default = null, bool $preview = false): mixed
    {
        $row = static::query()->where('setting_key', $key)->first();

        if (! $row) {
            return $default;
        }

        if ($preview) {
            return $row->value_draft ?? $row->value ?? $default;
        }

        return $row->value ?? $default;
    }

    /**
     * Sama seperti get(), tapi selalu dikembalikan sebagai boolean —
     * dipakai untuk setting seperti `animations_enabled`.
     */
    public static function getBool(string $key, bool $default = true, bool $preview = false): bool
    {
        $value = static::get($key, $default ? '1' : '0', $preview);

        return (bool) $value;
    }

    /**
     * Simpan nilai baru sebagai DRAFT (bukan langsung ke `value` live).
     * Baris dibuat otomatis kalau belum ada (updateOrCreate).
     */
    public static function setDraft(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['setting_key' => $key],
            ['value_draft' => is_bool($value) ? ($value ? '1' : '0') : $value]
        );
    }

    /**
     * Apakah ada minimal 1 baris dengan draft pending (value_draft NOT NULL)?
     * Dipakai untuk indikator status Draft/Live di admin.
     */
    public static function hasPendingDraft(): bool
    {
        return static::query()->whereNotNull('value_draft')->exists();
    }

    /**
     * Publish semua draft pending: value_draft -> value, lalu value_draft
     * di-NULL-kan. Dipanggil oleh Admin\AppearanceController@publish.
     */
    public static function publishAll(): void
    {
        static::query()->whereNotNull('value_draft')->get()->each(function (self $row) {
            $row->value = $row->value_draft;
            $row->value_draft = null;
            $row->save();
        });
    }

    /**
     * Buang semua draft pending tanpa menerapkannya — value live TIDAK
     * berubah. Dipanggil oleh Admin\AppearanceController@discardDraft.
     */
    public static function discardAllDrafts(): void
    {
        static::query()->whereNotNull('value_draft')->update(['value_draft' => null]);
    }
}

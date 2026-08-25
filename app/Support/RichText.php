<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Iterasi 27 (Fase 5) — sanitasi HTML body section artikel Blog (Tiptap).
 * Lihat docs/RENCANA-PENYEMPURNAAN-ADMIN.md bagian 3 baris "Sanitasi HTML
 * rich text".
 *
 * Konten HANYA pernah ditulis 1 admin terautentikasi (bukan input publik),
 * risiko XSS rendah — TAPI tetap disaring sbg pertahanan berlapis, BUKAN
 * cuma percaya output Tiptap apa adanya (mis. request langsung via
 * curl/DevTools ke endpoint yang sama bisa mem-bypass editor sepenuhnya).
 *
 * Pakai DOMDocument (ext-dom, bawaan PHP, TIDAK butuh package Composer
 * baru) — BUKAN `strip_tags($html, $allowed)` saja, krn `strip_tags()`
 * cuma membuang TAG yang tidak diizinkan, TAPI tetap mempertahankan SEMUA
 * atribut di tag yang diizinkan (mis. `<p onclick="...">` tetap lolos
 * krn `<p>` diizinkan, walau atributnya berbahaya) — celah yang gampang
 * terlewat kalau cuma mengandalkan whitelist tag tanpa whitelist atribut.
 */
class RichText
{
    private const ALLOWED_TAGS = ['p', 'strong', 'em', 'u', 'ul', 'ol', 'li', 'a', 'blockquote', 'code', 'br'];

    public static function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        // Prefix XML encoding declaration = trik standar supaya DOMDocument
        // menginterpretasi input sbg UTF-8 (default-nya ISO-8859-1, akan
        // merusak karakter multi-byte spt tanda kutip lengkung/emoji).
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>'.$html.'</div>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $wrapper = $dom->getElementsByTagName('div')->item(0);

        if (! $wrapper) {
            return '';
        }

        self::cleanNode($dom, $wrapper);

        $output = '';
        foreach (iterator_to_array($wrapper->childNodes) as $child) {
            $output .= $dom->saveHTML($child);
        }

        return trim($output);
    }

    /**
     * Rekursif: hapus tag tak dikenal (isi teksnya "diangkat" ke parent,
     * BUKAN ikut terhapus — cuma tag pembungkusnya yang dibuang), hapus
     * SEMUA atribut dari tag yang diizinkan, lalu KHUSUS `<a>` bangun ulang
     * `href` dari nilai yang sudah divalidasi (bukan dipertahankan mentah).
     */
    private static function cleanNode(DOMDocument $dom, DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMText) {
                continue;
            }

            if (! $child instanceof DOMElement) {
                $node->removeChild($child);

                continue;
            }

            $tag = strtolower($child->tagName);

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);

                continue;
            }

            $href = $tag === 'a' ? self::sanitizeHref($child->getAttribute('href')) : null;

            while ($child->attributes->length > 0) {
                $child->removeAttribute($child->attributes->item(0)->name);
            }

            if ($tag === 'a') {
                if ($href === null) {
                    // href tidak valid/aman — "a" dipertahankan sbg teks
                    // biasa (bukan link mati tanpa href), sama pola dgn tag
                    // tak dikenal di atas.
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);

                    continue;
                }

                $child->setAttribute('href', $href);
                $child->setAttribute('target', '_blank');
                $child->setAttribute('rel', 'noopener noreferrer nofollow');
            }

            self::cleanNode($dom, $child);
        }
    }

    /**
     * HANYA loloskan URL absolut http(s) atau path relatif diawali "/" —
     * skema lain (`javascript:`, `data:`, `vbscript:`, dst) ditolak.
     */
    private static function sanitizeHref(string $href): ?string
    {
        $href = trim($href);

        if ($href === '') {
            return null;
        }

        if (str_starts_with($href, '/') && ! str_starts_with($href, '//')) {
            return $href;
        }

        $valid = filter_var($href, FILTER_VALIDATE_URL);

        if (! $valid) {
            return null;
        }

        $scheme = parse_url($href, PHP_URL_SCHEME);

        return in_array(strtolower((string) $scheme), ['http', 'https'], true) ? $href : null;
    }
}

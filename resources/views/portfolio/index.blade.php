@extends('layouts.app')

@section('content')
    {{--
        Iterasi 20 (Fase 4): refactor dari 7 blok @if/@include statis
        (urutan hardcode di file ini) jadi loop dinamis atas $orderedSections
        — collection nama partial view, sudah difilter (hanya section aktif)
        & diurutkan (sort_order efektif, draft-aware) oleh
        PortfolioController@index (lihat App\Http\Controllers\PortfolioController
        ::SECTION_PARTIALS & ::index()). Urutan DEFAULT (sort_order asli 0-6)
        menghasilkan urutan identik dengan versi statis sebelumnya: hero,
        about, projects, experience, blog, testimonials, contact.

        Navbar & footer TIDAK ikut loop ini — keduanya structural, di-include
        langsung dari layouts/app.blade.php, tidak pernah bisa dimatikan/
        direorder (lihat docs/RENCANA-PENGEMBANGAN.md).

        "about" dan "skills" tetap dua baris section_settings terpisah yang
        togglenya fungsional, tapi hidup dalam SATU partial/<section>
        (about.blade.php memuat copy About + matriks Skills sbg blok
        nested) — "skills" SENGAJA TIDAK ada di SECTION_PARTIALS (tidak
        punya posisi DOM independen utk direorder), lihat komentar di
        about.blade.php utk bagaimana flag "skills" ($sectionActive)
        dihormati di sana.
    --}}
    @foreach ($orderedSections as $partial)
        @include($partial)
    @endforeach
@endsection

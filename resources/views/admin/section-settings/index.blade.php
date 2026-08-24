@extends('admin.layouts.app')

@section('title', 'Pengaturan Section')

@section('content')
    <div data-reveal class="space-y-1">
        <h2 class="text-xl font-extrabold text-slate-900">Pengaturan Section</h2>
        <p class="text-sm text-slate-500">Nyalakan atau matikan section pada halaman publik (<code class="text-[11px] font-mono bg-slate-100 px-1.5 py-0.5 rounded">/</code>). Perubahan tersimpan otomatis begitu switch diklik — tidak perlu tombol simpan.</p>
        <p class="text-xs text-indigo-600 font-semibold">
            Halaman ini sekarang juga bisa diakses lewat menu
            <a href="{{ route('admin.appearance', ['tab' => 'sections']) }}" class="underline hover:text-indigo-800">Tampilan Halaman Index &rarr; Urutan & Isi Section</a>
            (Iterasi 18, Fase 4) — kedua tempat menampilkan data yang sama.
        </p>
    </div>

    @include('admin.section-settings._list')
@endsection

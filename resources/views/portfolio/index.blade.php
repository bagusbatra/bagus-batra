@extends('layouts.app')

@section('content')
    {{--
        Each section below is gated by its section_settings.is_active flag
        (see PortfolioController@index — $sectionActive). Navbar & footer
        are structural and always render, per docs/RENCANA-PENGEMBANGAN.md.

        "about" and "skills" are two separate toggleable rows but live in
        ONE partial/<section> (about.blade.php contains both the About
        copy and the Skills matrix as a nested block) — see the comment
        inside that partial for how the "skills" flag is honoured there.
    --}}
    @if ($sectionActive['hero'] ?? true)
        @include('portfolio.partials.hero')
    @endif
    @if ($sectionActive['about'] ?? true)
        @include('portfolio.partials.about')
    @endif
    @if ($sectionActive['projects'] ?? true)
        @include('portfolio.partials.projects')
    @endif
    @if ($sectionActive['playground'] ?? true)
        @include('portfolio.partials.playground')
    @endif
    @if ($sectionActive['experience'] ?? true)
        @include('portfolio.partials.experience')
    @endif
    @if ($sectionActive['blog'] ?? true)
        @include('portfolio.partials.blog')
    @endif
    @if ($sectionActive['testimonials'] ?? true)
        @include('portfolio.partials.testimonials')
    @endif
    @if ($sectionActive['contact'] ?? true)
        @include('portfolio.partials.contact')
    @endif
@endsection

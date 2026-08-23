@extends('layouts.app')

@section('content')
    @include('portfolio.partials.hero')
    @include('portfolio.partials.about')
    @include('portfolio.partials.projects')
    @include('portfolio.partials.playground')
    @include('portfolio.partials.experience')
    @include('portfolio.partials.blog')
    @include('portfolio.partials.testimonials')
    @include('portfolio.partials.contact')
@endsection

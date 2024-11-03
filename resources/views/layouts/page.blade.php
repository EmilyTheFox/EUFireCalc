<?php

/**
 * @var string $url
 * @var string $title
 * @var string $description
 * @var string $activeNavbarSection
 */

$url         ??= null;
$title       ??= null;
$description ??= null;
$activeNavbarSection ??= 'Home';

?>

@extends('layouts.app', [
    'url'         => $url,
    'title'       => $title,
    'description' => $description
])

@section('head')
    @parent
@endsection

@section('app-content')

    @include('common.navbar', [
        'activeSection' => $activeNavbarSection
    ])

    @yield('content')

@endsection
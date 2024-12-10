<?php

/**
 * @var string $url
 * @var string $title
 * @var string $description
 */

// Setup the url, title and description
$url         ??= 'https://eufirecalc.com/';
$title       ??= 'EUFireCalc';
$description ??= 'EUFireCalc is a collection of tools around Financial Independence / Retire Early (FIRE), with a focus on Europe.';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="author" content="EmilyTheFox">

    <meta name="subject" content="EUFireCalc">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#FF8200">

    <link rel="apple-touch-icon" href="/images/logo.png">
    <link rel="manifest" href="/manifest.json">

    @include('common.linkpreview', [
        'url' => $url,
        'title' => $title,
        'description' => $description
    ])

    <script src="https://kit.fontawesome.com/7dc3015a44.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('head')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @yield('app-content')
</body>
</html>
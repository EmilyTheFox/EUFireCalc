<?php
/** @var string $url */
/** @var string $title */
/** @var string $description */

$image = '/images/logo.png';

?>

<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
@isset($image)
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:width" content="64">
    <meta property="og:image:height" content="64">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
@isset($image)
    <meta name="twitter:image" content="{{ $image }}">
@endif
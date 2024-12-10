<?php
/** @var string $activeSection */

$activeSection ??= 'Home';

?>

<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item" href="#">
                <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="logo">
                <span class="nav-title">EU Fire Calc</span>
            </a>
            <a class="navbar-burger" role="button" data-target="navbarMenu" aria-label="menu" aria-expanded="false">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        
        <div id="navbarMenu" class="navbar-menu">
            <div class="navbar-end">
                <a class="navbar-item has-text-primary {{ $activeSection === 'Home' ? 'is-active' : ''}}" href="/">Home</a>
                <a class="navbar-item has-text-primary {{ $activeSection === 'Fire' ? 'is-active' : ''}}" href="/fire">Fire Calc</a>
                <a class="navbar-item has-text-primary {{ $activeSection === 'Maps' ? 'is-active' : ''}}" href="/maps">EU Maps</a>
                <a class="navbar-item has-text-primary {{ $activeSection === 'FAQ' ? 'is-active' : ''}}" href="/faq">FAQ</a>
            </div>
        </div>
    </div>
</nav>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <title>EUFireCalc - Home</title>
        <meta name="description" content="EUFireCalc is a collection of tools around Financial Independence / Retire Early (FIRE), with a focus on Europe.">
        <meta name="author" content="EmilyTheFox">

        <meta name="subject" content="EUFireCalc">
        <meta name="robots" content="index, follow">
        <meta name="theme-color" content="#FF8200">

        <meta property="og:title" content="EUFireCalc - A Retirement Planning Tool">
        <meta property="og:url" content="https://eufirecalc.com/">
        <meta property="og:description" content="EUFireCalc is a collection of tools around Financial Independence / Retire Early (FIRE), with a focus on Europe.">
        <meta property="og:image" content="/images/logo.png">
        <meta property="og:image:width" content="64">
        <meta property="og:image:height" content="64">

        <link rel="apple-touch-icon" href="/images/logo.png">
        <link rel="manifest" href="/manifest.json">
 
        <script src="https://kit.fontawesome.com/7dc3015a44.js" crossorigin="anonymous"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body>
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
                        <a class="navbar-item has-text-primary is-active" href="#">Home</a>
                        <a class="navbar-item has-text-primary" href="/fire">Fire Calc</a>
                        <a class="navbar-item has-text-primary" href="/maps">EU Maps</a>
                        <a class="navbar-item has-text-primary" href="/faq">FAQ</a>
                        <button style="margin: 0 6px; padding: 0 6px">
                            <svg width="30" height="30" id="light-icon" display="none">
                                <circle cx="15" cy="15" r="6" fill="currentColor" />
                                <line id="ray" stroke="currentColor" stroke-width="2" stroke-linecap="round" x1="15" y1="1" x2="15" y2="4"></line>
                                <use href="#ray" transform="rotate(45 15 15)" />
                                <use href="#ray" transform="rotate(90 15 15)" />
                                <use href="#ray" transform="rotate(135 15 15)" />
                                <use href="#ray" transform="rotate(180 15 15)" />
                                <use href="#ray" transform="rotate(225 15 15)" />
                                <use href="#ray" transform="rotate(270 15 15)" />
                                <use href="#ray" transform="rotate(315 15 15)" />
                            </svg>
                            <svg width="30" height="30" id="dark-icon" display="block">
                                <path fill="currentColor" d="M 23, 5 A 12 12 0 1 0 23, 25 A 12 12 0 0 1 23, 5"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="section">
                <div class="columns">
                    <div class="column has-text-centered">
                        <h1 class="title main-colored-text">EU Fire Calculator</h1><br>
                    </div>
                </div>
                <div id="app" class="row columns is-multiline">
                    <div v-for="card in cardData" key="card.id" class="column is-4">
                        <div class="card large">
                            <div class="card-image">
                                <figure class="image is-16by9">
                                <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="logo">
                                </figure>
                            </div>
                            <div class="card-content">
                                <div class="media">
                                    <div class="media-left">
                                        <figure class="image is-48x48">
                                        <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="logo">
                                        </figure>
                                    </div>
                                    <div class="media-content">
                                        <p class="title is-4 no-padding">Fire Calc</p>
                                        <p>
                                        <span class="title is-6">
                                            <a href="/fire"> Fire Calculator </a> </span> </p>
                                        <p class="subtitle is-6">See how your strategy holds up</p>
                                    </div>
                                </div>
                                <div class="content">
                                    Uwu
                                    <div class="background-icon"><span class="icon-twitter"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

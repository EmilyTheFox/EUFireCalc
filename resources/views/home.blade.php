@extends('layouts.page', [
    'url'         => 'https://eufirecalc.com/',
    'title'       => 'EUFireCalc - A Retirement Planning Tool',
    'description' => 'EUFireCalc is a collection of tools around Financial Independence / Retire Early (FIRE), with a focus on Europe.',
    'activeNavbarSection' => 'Home'
])

@section('content')
    <div class="container">
        <div class="section">
            <div class="columns">
                <div class="column has-text-centered">
                    <h1 class="title main-colored-text">EU Fire Calculator</h1><br>
                </div>
            </div>
            <div class="grid is-col-min-10">
                <div class="card cell">
                    <div class="card-image">
                        <figure class="image is-16by9">
                            <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="logo"/>
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
                                        <a href="/fire"> Fire Calculator </a>
                                    </span>
                                </p>
                                <p class="subtitle is-6">See how your strategy holds up</p>
                            </div>
                        </div>
                        <div class="content">
                            UwU
                        </div>
                    </div>
                </div>
                <div class="card cell">
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
                                <p class="title is-4 no-padding">Maps</p>
                                <p>
                                    <span class="title is-6">
                                        <a href="/maps"> Maps of Europe </a> 
                                    </span>
                                </p>
                                <p class="subtitle is-6">Get multiple maps of Europe on financial topics</p>
                            </div>
                        </div>
                        <div class="content">
                            Awoo
                        </div>
                    </div>
                </div>
                <div class="card cell">
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
                                <p class="title is-4 no-padding">FAQ</p>
                                <p>
                                <span class="title is-6">
                                    <a href="/faq"> Frequently Asked Questions </a> </span> </p>
                                <p class="subtitle is-6">Details about how this fire simulator works</p>
                            </div>
                        </div>
                        <div class="content">
                            OwO
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

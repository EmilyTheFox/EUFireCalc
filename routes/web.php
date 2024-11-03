<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('home'); });
Route::get('/fire', function () { return view('fire'); });

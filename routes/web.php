<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('public.home'))->name('home');
Route::get('/standings', fn () => view('public.standings'))->name('standings');
Route::get('/schedule', fn () => view('public.schedule'))->name('schedule');
Route::get('/big-shots', fn () => view('public.big-shots'))->name('big-shots');
Route::get('/players', fn () => view('public.players'))->name('players');
Route::get('/venues', fn () => view('public.venues'))->name('venues');
Route::get('/league-info', fn () => view('public.league-info'))->name('league-info');

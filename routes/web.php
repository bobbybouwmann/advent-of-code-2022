<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/1-1', function () {
    $input = collect(explode(PHP_EOL . PHP_EOL, Storage::get('input/day1.txt')));

    return $input->map(function ($result) {
        return array_sum(
            explode(PHP_EOL, trim($result))
        );
    })->sortByDesc(function ($result) {
        return $result;
    })->first();
});

Route::get('/1-2', function () {
    $input = collect(explode(PHP_EOL . PHP_EOL, Storage::get('input/day1.txt')));

    return $input->map(function ($result) {
        return array_sum(
            explode(PHP_EOL, trim($result))
        );
    })->sortByDesc(function ($result) {
        return $result;
    })->take(3)->sum();
});

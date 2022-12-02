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
    $groupedRoutes = collect(Route::getRoutes()->getRoutes())
        ->filter(function (\Illuminate\Routing\Route $route) {
            return preg_match('~[0-9]+~', $route->uri());
        })->groupBy(function (\Illuminate\Routing\Route $route) {
            return explode('-', $route->uri())[0];
        });

    return view('welcome', ['groupedRoutes' => $groupedRoutes]);
});

Route::get('/1-1', function () {
    $input = collect(explode(PHP_EOL . PHP_EOL, Storage::get('input/day1.txt')));

    $result = $input->map(function ($result) {
        return array_sum(
            explode(PHP_EOL, trim($result))
        );
    })->sortByDesc(function ($result) {
        return $result;
    })->first();

    return view('result', ['result' => $result]);
});

Route::get('/1-2', function () {
    $input = collect(explode(PHP_EOL . PHP_EOL, Storage::get('input/day1.txt')));

    $result = $input->map(function ($result) {
        return array_sum(
            explode(PHP_EOL, trim($result))
        );
    })->sortByDesc(function ($result) {
        return $result;
    })->take(3)->sum();

    return view('result', ['result' => $result]);
});

Route::get('/2-1', function () {
    $input = collect(explode(PHP_EOL, trim(Storage::get('input/day2.txt'))));

    $win = 6;
    $draw = 3;
    $scores = [
        'A' => 1,
        'B' => 2,
        'C' => 3,
        'X' => 1,
        'Y' => 2,
        'Z' => 3,
    ];

    $result = $input->map(function ($game) use ($win, $draw, $scores) {
        $score = 0;

        $hands = explode(' ', $game);
        $theirs = strtoupper($hands[0]);
        $mine = strtoupper($hands[1]);

        // Add default score for hand
        $score += $scores[$mine];

        // Draw
        if ($scores[$theirs] === $scores[$mine]) {
            return $score += $draw;
        }

        // Win
        if (($mine === 'X' && $theirs === 'C')
            || ($mine === 'Y' && $theirs === 'A')
            || ($mine === 'Z' && $theirs === 'B')
        ) {
            return $score += $win;
        }

        // Everything else is defeat
        return $score;
    })->sum();

    return view('result', ['result' => $result]);
});

<?php

use Illuminate\Support\Collection;
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

    function score(string $letter): int
    {
        return match ($letter) {
            'A', 'X' => 1, // Rock
            'B', 'Y' => 2, // Paper
            'C', 'Z' => 3, // Scissors
        };
    }

    $result = $input->map(function ($game) {
        [$theirs, $mine] = explode(' ', $game);

        // Add default score for hand
        $score = score($mine);

        // Draw = score + 3
        if (score($theirs) === score($mine)) {
            return $score + 3;
        }

        // Win = score + 6
        if (($mine === 'X' && $theirs === 'C')
            || ($mine === 'Y' && $theirs === 'A')
            || ($mine === 'Z' && $theirs === 'B')
        ) {
            return $score + 6;
        }

        // Loss = score + 0
        return $score;
    })->sum();

    return view('result', ['result' => $result]);
});

Route::get('/2-2', function () {
    $input = collect(explode(PHP_EOL, trim(Storage::get('input/day2.txt'))));

    function score(string $letter): int
    {
        return match ($letter) {
            'A', 'D' => 1, // Rock
            'B', 'E' => 2, // Paper
            'C', 'F' => 3, // Scissors
        };
    }

    function mine(string $letter, string $theirs): string
    {
        // Win
        if ($letter === 'Z') {
            return match ($theirs) {
                'A' => 'E',
                'B' => 'F',
                'C' => 'D',
            };
        }

        // Draw
        if ($letter === 'Y') {
            return match ($theirs) {
                'A' => 'D',
                'B' => 'E',
                'C' => 'F',
            };
        }

        // Lose
        return match ($theirs) {
            'A' => 'F',
            'B' => 'D',
            'C' => 'E',
        };
    }

    $result = $input->map(function ($game) {
        [$theirs, $decision] = explode(' ', $game);

        $mine = mine($decision, $theirs);

        // Add default score for hand
        $score = score($mine);

        // Draw = score + 3
        if (score($theirs) === score($mine)) {
            return $score + 3;
        }

        // Win = score + 6
        if (($mine === 'D' && $theirs === 'C')
            || ($mine === 'E' && $theirs === 'A')
            || ($mine === 'F' && $theirs === 'B')
        ) {
            return $score + 6;
        }

        // Loss = score + 0
        return $score;
    })->sum();

    return view('result', ['result' => $result]);
});

Route::get('/3-1', function () {
    $input = collect(explode(PHP_EOL, trim(Storage::get('input/day3.txt'))));

    $result = $input->map(function (string $string) {
        [$part1, $part2] = str_split($string, strlen($string) / 2);

        return collect(str_split($part1))
            ->intersect(collect(str_split($part2)))
            ->first();
    })->map(function (string $letter) {
        $letters = array_merge(range('a', 'z'), range('A', 'Z'));

        return array_search($letter, $letters) + 1;
    })->sum();

    return view('result', ['result' => $result]);
});

Route::get('/3-2', function () {
    $input = collect(explode(PHP_EOL, trim(Storage::get('input/day3.txt'))));

    $result = $input->chunk(3)
        ->map(function (Collection $collection) {
            return $collection->reduce(function ($intersection, $items) {
                $intersection = $intersection ?? collect(str_split($items));
                return $intersection->intersect(collect(str_split($items)));
            })->unique()->first();
        })
        ->map(function (string $letter) {
            $letters = array_merge(range('a', 'z'), range('A', 'Z'));
            return array_search($letter, $letters) + 1;
        })->sum();

    return view('result', ['result' => $result]);
});

Route::get('/4-1', function () {
    $input = collect(explode(PHP_EOL, trim(Storage::get('input/day4.txt'))));

    $result = $input->filter(function ($item) {
        [$one, $two] = explode(',', $item);

        [$startOne, $endOne] = explode('-', $one);
        [$startTwo, $endTwo] = explode('-', $two);

        $rangeOne = range($startOne, $endOne);
        $rangeTwo = range($startTwo, $endTwo);

        return count(array_intersect($rangeOne, $rangeTwo)) === count($rangeOne)
            || count(array_intersect($rangeTwo, $rangeOne)) === count($rangeTwo);
    })->count();

    return view('result', ['result' => $result]);
});

Route::get('/4-2', function () {
    $input = collect(explode(PHP_EOL, trim(Storage::get('input/day4.txt'))));

    $result = $input->filter(function ($item) {
        [$one, $two] = explode(',', $item);

        [$startOne, $endOne] = explode('-', $one);
        [$startTwo, $endTwo] = explode('-', $two);

        $rangeOne = range($startOne, $endOne);
        $rangeTwo = range($startTwo, $endTwo);

        return count(array_intersect($rangeOne, $rangeTwo))
            || count(array_intersect($rangeTwo, $rangeOne));
    })->count();

    return view('result', ['result' => $result]);
});

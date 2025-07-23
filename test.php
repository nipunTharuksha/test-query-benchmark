<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


Route::get('/home', function () {
    /*  $perPage = 10;
        $targetPage = 1000;
    
        $cursor = null;
    
        for ($i = 1; $i <= $targetPage; $i++) {
            $paginator = User::orderBy('id')->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
            $cursor = $paginator->nextCursor()?->encode();
    
            if (!$cursor) {
                return response()->json([
                    'message' => "Reached end of records before page $targetPage.",
                    'last_available_page' => $i,
                    'cursor' => null,
                ]);
            }
        }
    
        return response()->json([
            'page' => $targetPage,
            'cursor_token' => $cursor,
        ]);*/

    $page = 1000;
    $perPage = 10;
    $results = [];

    // PAGINATE
    DB::enableQueryLog();
    $start = hrtime(true);
    User::orderBy('id')->paginate($perPage, ['*'], 'page', $page);
    $elapsed = (hrtime(true) - $start) / 1_000_000;
    $results['paginate'] = [
        'time_ms' => round($elapsed, 3) . ' ms',
        'queries' => collect(DB::getQueryLog())->pluck('query')->toArray(),
    ];

    DB::flushQueryLog();

    // SIMPLE PAGINATE
    DB::enableQueryLog();
    $start = hrtime(true);
    User::orderBy('id')->simplePaginate($perPage, ['*'], 'page', $page);
    $elapsed = (hrtime(true) - $start) / 1_000_000;
    $results['simplePaginate'] = [
        'time_ms' => round($elapsed, 3) . ' ms',
        'queries' => collect(DB::getQueryLog())->pluck('query')->toArray(),
    ];
    DB::flushQueryLog();


    DB::enableQueryLog();
    $start = hrtime(true);
    User::orderBy('id')->cursorPaginate(
        $perPage,
        ['*'],
        'cursor',
        'eyJpZCI6MTAwMDAsIl9wb2ludHNUb05leHRJdGVtcyI6dHJ1ZX0'
    );

    $elapsed = (hrtime(true) - $start) / 1_000_000;
    $results['cursorPaginate'] = [
        'time_ms' => round($elapsed, 3) . ' ms',
        'queries' => collect(DB::getQueryLog())->pluck('query')->toArray(),
    ];
    DB::flushQueryLog();

    return response()->json($results);
});


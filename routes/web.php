<?php

use App\Models\VUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

//https://www.youtube.com/watch?v=VE3bJIC9Swk&ab_channel=KawanKoding

Route::get('/', function () {
    return view('welcome');
});

Route::get('/data', function (Request $r) {
    if ($r->ajax()) {

        // $data = DB::table('v_users'); //success
        // return DataTables::of($data) //success
        // $data = DB::select('select * from v_users'); //error
        // return DataTables::of($data) //error

        $data = VUser::query(); //success
        return DataTables::eloquent($data) //success
            ->addColumn('action', 'partial-action')
            ->filter(function ($query) {
                if (request()->has('name')) {
                    $query->where('name', 'like', "%" . request('name') . "%");
                }

                if (request()->has('email')) {
                    $query->where('email', 'like', "%" . request('email') . "%");
                }
            }, true)
            ->order(function ($query) {
                if (request()->has('name')) {
                    $query->orderBy('name', 'asc');
                }
            })
            ->toJson();
    }
})->name('data');


////////////////////////////////////////
/////////BACA INI UNTUK PENCERAHAN//////
////////////////////////////////////////
//cara 1
// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/data', function () {
//     // return DataTables::of(VUser::query())->make(true);
//     $data = VUser::query();
//     return DataTables::eloquent($data)
//         ->addColumn('action', 'partial-action')
//         ->make(true);
//     //jika di atas tidak render action pakai cara ini
//     // return DataTables::eloquent($data)
//     //     ->addColumn('action', 'partial-action')
//     //     ->rawColumns(['link', 'action'])
//     //     ->toJson();
// })->name('data');



//cara 2
// Route::get('/', function (Request $r) {
//     if ($r->ajax()) {
//         $data = VUser::query();
//         return DataTables::eloquent($data)
//             ->addColumn('action', 'partial-action')
//             ->make(true);
//     }

//     return view('welcome');
// })->name('data');

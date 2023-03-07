<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Rap2hpoutre\FastExcel\FastExcel;

//https://github.com/rap2hpoutre/fast-excel#export-large-collections-with-chunk

class UserController extends Controller
{
    public function export()
    {
        $date = date('Y-m-d');
        $path = (new FastExcel($this->exportGenerator()))->export($date . '.xlsx');
        $path = substr($path, strrpos($path, '/') + 1);
        // return $path;
        $file = public_path() . "/" . $path;
        $headers = array('Content-Type: application/pdf',);
        return Response::download($file, $path, $headers);
    }

    function exportGenerator()
    {
        // $users = User::cursor();
        // $users = DB::table('users')->select('*')->cursor();
        // $users = DB::table('users')->select('*')->limit(1000)->cursor();
        $users = DB::cursor("SELECT * FROM users");
        foreach ($users as $user) {
            yield $user;
        }
    }
}

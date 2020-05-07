<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    //学校列表获取
    public function getschoollist()
    {
        $list = DB::select('select schoolname as text, province, id  from schools where status = :status', ['status' => 1]);

        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }
}

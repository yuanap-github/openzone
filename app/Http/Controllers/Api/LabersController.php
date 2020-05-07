<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabersController extends Controller
{

    //版块标签列表获取(首页)
    public function gethomelaberlist()
    {
        $list = DB::select('select id,labername,introduce from labers where status != :status', ['status' => 0]);

        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }

    //版块标签列表获取(发布)
    public function getaddlaberlist(Request $request)
    {
        $official = $request->input('official');
        if (!empty($official) && $official == 1) {
            $list = DB::select('select id,labername,introduce from labers where status != :status', ['status' => 0]);
        } else {
            $list = DB::select('select id,labername,introduce from labers where status = :status', ['status' => 1]);
        }
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }
}

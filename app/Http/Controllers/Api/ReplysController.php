<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReplysController extends Controller
{
    public function getreplyslist(Request $request)
    {
        $comments_id = $request->input('comments_id');
        $page        = $request->input('page');
        $comments    = DB::select('select * from comments where id = ?', [$comments_id]);
        if (count($comments) == 0) {
            return response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10005'], 'data' => null]);
        }
        $list = DB::table('replys')
            ->leftJoin('comments', 'comments.id', '=', 'replys.comments_id')
            ->leftJoin('users', 'users.id', '=', 'replys.user_id')
            ->leftJoin('schools', 'users.school', '=', 'schools.id')
            ->where('replys.comments_id', $comments_id)
            ->where('replys.status', 1)
            ->select(
                'replys.replys_text', 'replys.praise_number as replys_praise_number', 'replys.created_at as replys_time', 'replys.id as replys_id',
                'users.nickname as user_nickname', 'users.headportrait as user_headportrait', 'users.school as user_school', 'users.sex as user_sex', 'users.official',
                'schools.schoolname'
            )
            ->orderby('replys.id', 'desc')
            ->skip($page)
            ->paginate(config('config.page'));
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }

    public function addreplys(Request $request)
    {
        $token       = $request->input('token');
        $user_id     = $request->input('user_id');
        $works_id    = $request->input('works_id');
        $replys_text = $request->input('replys_text');
        $comments_id = $request->input('comments_id');
        $date        = date('Y-m-d H:i:s');
        $works       = DB::select('select * from works where id = ? and status = ?', [$works_id, 1]);
        if (count($works) == 0) {
            return response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10005'], 'data' => null]);
        }
        $comments = DB::select('select * from comments where id = ?', [$comments_id]);
        if (count($comments) == 0) {
            return response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10005'], 'data' => null]);
        }
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        $id = DB::table('replys')->insertGetId(
            [
                'works_id'      => $works_id,
                'replys_text'   => $replys_text,
                'comments_id'   => $comments_id,
                'user_id'       => $user_id,
                'praise_number' => 0,
                'created_at'    => $date,
                'updated_at'    => $date,
            ]
        );
        $replys_count = DB::table('replys')->where('comments_id', $comments_id)->where('status', 1)->count();
        return $id > 0 ? response()->json(['status' => 1, 'msg' => 'ok', 'data' => $replys_count]) : response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10004'], 'data' => null]);
    }
}

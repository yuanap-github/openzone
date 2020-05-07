<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentsController extends Controller
{
    public function getcommentslist(Request $request)
    {
        $works_id = $request->input('works_id');
        $page     = $request->input('page');
        $works    = DB::select('select * from works where id = ? and status = ?', [$works_id, 1]);
        if (count($works) == 0) {
            return response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10005'], 'data' => null]);
        }
        DB::enableQueryLog();
        $replys_table = DB::table('replys')->select('comments_id', DB::raw('count(comments_id) as replys_count_number'))->groupBy('comments_id');
        $list         = DB::table('comments')
            ->leftJoin('works', 'works.id', '=', 'comments.works_id')
            ->leftJoin('users', 'users.id', '=', 'comments.user_id')
            ->leftJoin('schools', 'users.school', '=', 'schools.id')
            ->leftjoinSub($replys_table, 'replys_table', function ($join) {
                $join->on('comments.id', '=', 'replys_table.comments_id');
            })
            ->where('works.id', $works_id)
            ->where('works.status', 1)
            ->select('works.*',
                'comments.comments_text', 'comments.praise_number as comments_praise_number', 'comments.created_at as comments_time', 'comments.id as comments_id',
                'users.nickname as user_nickname', 'users.headportrait as user_headportrait', 'users.school as user_school', 'users.sex as user_sex', 'users.official',
                'schools.schoolname', DB::raw('ifnull(replys_table.replys_count_number, 0) as replys_count')
            )
            ->orderby('comments.id', 'desc')
            ->skip($page)
            ->paginate(config('config.page'));
        $list = $list->toArray();
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }

    public function addcomments(Request $request)
    {
        $token         = $request->input('token');
        $user_id       = $request->input('user_id');
        $works_id      = $request->input('works_id');
        $comments_text = $request->input('comments_text');
        $date          = date('Y-m-d H:i:s');
        $works         = DB::select('select * from works where id = ? and status = ?', [$works_id, 1]);
        if (count($works) == 0) {
            return response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10005'], 'data' => null]);
        }
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        $id = DB::table('comments')->insertGetId(
            [
                'works_id'      => $works_id,
                'comments_text' => $comments_text,
                'user_id'       => $user_id,
                'praise_number' => 0,
                'created_at'    => $date,
                'updated_at'    => $date,
            ]
        );
        $replys_table = DB::table('replys')->select('comments_id', DB::raw('count(comments_id) as replys_count_number'))->groupBy('comments_id');
        $list         = DB::table('comments')
            ->leftJoin('works', 'works.id', '=', 'comments.works_id')
            ->leftJoin('users', 'users.id', '=', 'comments.user_id')
            ->leftJoin('schools', 'users.school', '=', 'schools.id')
            ->leftjoinSub($replys_table, 'replys_table', function ($join) {
                $join->on('comments.id', '=', 'replys_table.comments_id');
            })
            ->where('comments.id', $id)
            ->where('works.status', 1)
            ->select('works.*',
                'comments.comments_text', 'comments.praise_number as comments_praise_number', 'comments.created_at as comments_time', 'comments.id as comments_id',
                'users.nickname as user_nickname', 'users.headportrait as user_headportrait', 'users.school as user_school', 'users.sex as user_sex', 'users.official',
                'schools.schoolname'
            )
            ->orderby('id', 'desc')
            ->skip(1)
            ->paginate(1);
        $list = $list->toArray();
        return $id > 0 ? response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]) : response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10004'], 'data' => null]);
    }
}

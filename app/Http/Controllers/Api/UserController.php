<?php

namespace App\Http\Controllers\Api;

use App\Common\Oss;
use App\Common\Sms;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function checktouken(Request $request)
    {
        $token   = $request->input('token');
        $user_id = $request->input('user_id');
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 10003, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => null]);
    }
    //发送验证码
    public function sendsms(Request $request)
    {
        //参数
        $phone = $request->input('phone');
        //格式错误校验
        if (!CheckPhone($phone)) {
            return response()->json(['status' => 10001, 'msg' => config('config.ErroorCode')['10001'], 'data' => null]);
        }
        try {
            //验证码发送
            $result = Sms::sendsms($phone);
            if (isset($result["Message"]) && $result["Message"] != "OK") {
                return response()->json(['status' => 0, 'msg' => $result["Message"], 'data' => null]);
            } else {
                Cache::put($phone, $result["code_number"], 6000);
                return response()->json(['status' => 1, 'msg' => 'ok', 'data' => ['phone' => $phone, 'code' => $result['code_number']]]);
            }
        } catch (ClientException $e) {
            //意外抛错
            return ['status' => 0, 'msg' => $e->getErrorMessage() . PHP_EOL];
        }
    }
    //校验验证码
    public function checkphonecode(Request $request)
    {
        //参数
        $phone     = $request->input('phone');
        $phonecode = $request->input('phonecode');
        //格式错误校验
        if (!CheckPhone($phone)) {
            return response()->json(['status' => 10001, 'msg' => config('config.ErroorCode')['10001'], 'data' => null]);
        }
        //校验缓存验证码
        if (Cache::has($phone) && $phonecode == Cache::get($phone)) {
            //清除缓存
            // Cache::pull($phone);
            $token        = md5(time() . $phone);
            $date         = date('Y-m-d H:i:s');
            $headportrait = 'http://bbshonglanxiaoyuan.oss-cn-hangzhou.aliyuncs.com/default.jpg';
            //查询是否新用户
            $users = DB::select('select * from users where phone = ?', [$phone]);
            if (count($users) == 0) {
                //新用户插入
                $id = DB::table('users')->insertGetId(
                    [
                        'nickname'     => substr($phone, -4),
                        'headportrait' => $headportrait,
                        'phone'        => $phone,
                        'school'       => '',
                        'sex'          => 0,
                        'token'        => $token,
                        'created_at'   => $date,
                        'updated_at'   => $date,
                    ]
                );
                $return_data = DB::table('users')->where('id', $id)->first();
            } else {
                //老用户更新
                $id          = DB::update("update users set token = '{$token}', updated_at = '{$date}' where phone = ?", [$phone]);
                $return_data = DB::table('users')
                    ->leftJoin('schools', 'users.school', '=', 'schools.id')
                    ->where('users.phone', $phone)
                    ->select('users.*', 'schools.schoolname')
                    ->first();
            }
            return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $return_data]);
        } else {
            //错误验证码返回
            return response()->json(['status' => 10002, 'msg' => config('config.ErroorCode')['10002'], 'data' => null]);
        }
    }

    // test oss pone
    public function osstest()
    {
        Cache::put(18255187911, 0000, 60 * 60 * 24);
        // $filePath = '/home/bbs/bbs/public/upload/images/default.jpg';
        // var_dump(strtr(Oss::uplodeoss($filePath, 'default.jpg'), 'http', 'https'));
    }

    //个人信息修改
    public function updateuserinfo(Request $request)
    {
        //参数
        $imgBase64 = $request->input('file');
        $nickname  = $request->input('nickname');
        $sex       = $request->input('sex');
        $school_id = $request->input('school_id');
        $token     = $request->input('token');
        $user_id   = $request->input('user_id');
        $date      = date('Y-m-d H:i:s');
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 10003, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        if (!empty($imgBase64)) {
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $imgBase64['content'], $result)) {
                $type     = $result[2];
                $new_file = config('config.headportraittmp');
                if (!file_exists($new_file)) {
                    mkdir($new_file, 0777);
                }
                $file     = md5($user_id . time()) . ".{$type}";
                $new_file = $new_file . '/' . $file;
                if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $imgBase64['content'])))) {
                    $filePath      = $new_file;
                    $oss_file_name = Oss::uplodeoss($filePath, $file);
                    $headportrait  = $oss_file_name;
                }
            }
        } else {
            $headportrait = $users[0]->headportrait;
        }
        $id          = DB::update("update users set headportrait = '{$headportrait}', nickname = '{$nickname}', school = {$school_id}, sex = {$sex}, updated_at = '{$date}' where id = ?", [$user_id]);
        $return_data = DB::table('users')
            ->leftJoin('schools', 'users.school', '=', 'schools.id')
            ->where('users.id', $user_id)
            ->select('users.*', 'schools.schoolname')
            ->first();
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $return_data]);

    }

    //校验昵称是否重复
    public function checknickname(Request $request)
    {

        $token    = $request->input('token');
        $user_id  = $request->input('user_id');
        $nickname = trim($request->input('nickname'));
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 10003, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        $nickname = DB::select('select * from users where nickname = ? and id != ?', [$nickname, $user_id]);
        if (count($nickname) != 0) {
            return response()->json(['status' => 1, 'msg' => 'ok', 'data' => false]);
        } else {
            return response()->json(['status' => 1, 'msg' => 'ok', 'data' => true]);
        }
    }

}

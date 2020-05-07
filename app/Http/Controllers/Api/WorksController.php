<?php

namespace App\Http\Controllers\Api;

use App\Common\Oss;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorksController extends Controller
{
    //作品发布
    public function worksadd(Request $request)
    {
        //参数
        $content   = !empty($request->input('content')) ? $request->input('content') : '';
        $imgBase64 = $request->input('file');
        $token     = $request->input('token');
        $user_id   = $request->input('user_id');
        $laber_id  = $request->input('laber_id');
        $images    = [];
        $date      = date('Y-m-d H:i:s');
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        if (count($imgBase64['data']) > 0) {
            foreach ($imgBase64['data'] as $key => $base64) {
                # code...
                if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64['content'], $result)) {
                    $type     = $result[2];
                    $new_file = config('config.workstmp');
                    if (!file_exists($new_file)) {
                        mkdir($new_file, 0777);
                    }
                    $file     = md5($user_id . time() . $key) . ".{$type}";
                    $new_file = $new_file . '/' . $file;
                    if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64['content'])))) {
                        $filePath      = $new_file;
                        $oss_file_name = Oss::uplodeoss($filePath, $file);
                        array_push($images, $oss_file_name);
                    }
                }
            }
        }
        try {
            $id = DB::table('works')->insertGetId(
                [
                    'user_id'    => $users[0]->id,
                    'content'    => $content,
                    'school_id'  => $users[0]->school,
                    'images'     => implode(',', $images),
                    'laber_id'   => $laber_id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );
            return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $id]);
        } catch (ClientException $e) {
            return response()->json(['status' => 10003, 'msg' => config('config.ErroorCode')['10004'], 'data' => null]);
        }
    }

    //我的作品
    public function myworkslist(Request $request)
    {
        //参数
        $token   = $request->input('token');
        $user_id = $request->input('user_id');
        $page    = $request->input('page');
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        $list = DB::table('works')
            ->leftJoin('users', 'works.user_id', '=', 'users.id')
            ->leftJoin('labers', 'works.laber_id', '=', 'labers.id')
            ->where('works.user_id', $user_id)
            ->where('works.status', 1)
            ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex', 'users.official', 'labers.labername')
            ->orderby('id', 'desc')
            ->skip($page)
            ->paginate(config('config.page'));
        if (count($list) > 0) {
            foreach ($list as $tmp) {
                $tmp->img_path       = explode(',', $tmp->images);
                $tmp->comments_count = DB::table('comments')->where('works_id', $tmp->id)->where('status', 1)->count();
                $praise              = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $tmp->id, 1]);
                if (count($praise) > 0) {
                    $tmp->praise = true;
                } else {
                    $tmp->praise = false;
                }
                unset($tmp->images);
            }
        }
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }

    //我点赞的作品
    public function mypraisesworkslist(Request $request)
    {
        $token   = $request->input('token');
        $user_id = $request->input('user_id');
        $page    = $request->input('page');
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        $list = DB::table('praises')
            ->leftJoin('works', 'works.id', '=', 'praises.works_id')
            ->leftJoin('users', 'praises.beuser_id', '=', 'users.id')
            ->leftJoin('labers', 'works.laber_id', '=', 'labers.id')
            ->where('praises.user_id', $user_id)
            ->where('praises.status', 1)
            ->where('works.status', 1)
            ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex', 'users.official', 'labers.labername')
            ->orderby('id', 'desc')
            ->skip($page)
            ->paginate(config('config.page'));
        if (count($list) > 0) {
            foreach ($list as $tmp) {
                $tmp->img_path       = explode(',', $tmp->images);
                $tmp->comments_count = DB::table('comments')->where('works_id', $tmp->id)->where('status', 1)->count();
                $praise              = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $tmp->id, 1]);
                if (count($praise) > 0) {
                    $tmp->praise = true;
                } else {
                    $tmp->praise = false;
                }
                unset($tmp->images);
            }
        }
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }

    //作品删除
    public function deletemyworks(Request $request)
    {
        //参数
        $token   = $request->input('token');
        $user_id = $request->input('user_id');
        $id      = $request->input('id');
        $date    = date('Y-m-d H:i:s');
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        $works = DB::select('select * from works where id = ? and status = ? and user_id = ?', [$id, 1, $user_id]);
        if (count($works) == 0) {
            return response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10005'], 'data' => null]);
        }
        $id = DB::update("update works set status = 0, updated_at = '{$date}' where  id= ? and user_id", [$id, $user_id]);
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => null]);
    }

    //已登录首页里展示全部作品
    public function getmyschoolworkslist(Request $request)
    {
        //参数
        $token    = $request->input('token');
        $user_id  = $request->input('user_id');
        $id       = $request->input('id');
        $page     = $request->input('page');
        $laber_id = $request->input('laber_id');
        $date     = date('Y-m-d H:i:s');
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        if (!empty($users[0]->school) && $users[0]->school != 0) {
            $laber = DB::select('select id,labername from labers where status != :status', ['status' => 0]);
            if ($laber_id != $laber[0]->id) {
                $list = DB::table('works')
                    ->leftJoin('users', 'works.user_id', '=', 'users.id')
                    ->where('users.school', $users[0]->school)
                    ->where('works.laber_id', $laber_id)
                    ->where('works.status', 1)
                // ->where('user_id', '!=', $user_id)
                    ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex')
                    ->orderby('id', 'desc')
                    ->skip($page)
                    ->paginate(config('config.page'));
                if (count($list) > 0) {
                    foreach ($list as $tmp) {
                        $tmp->img_path       = explode(',', $tmp->images);
                        $tmp->comments_count = DB::table('comments')->where('works_id', $tmp->id)->where('status', 1)->count();
                        $praise              = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $tmp->id, 1]);
                        if (count($praise) > 0) {
                            $tmp->praise = true;
                        } else {
                            $tmp->praise = false;
                        }
                        $browses = DB::select('select * from browses where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $tmp->user_id, $tmp->id]);
                        if (count($browses) == 0) {
                            $id = DB::table('browses')->insertGetId(
                                [
                                    'user_id'    => $user_id,
                                    'beuser_id'  => $tmp->user_id,
                                    'works_id'   => $tmp->id,
                                    'status'     => 1,
                                    'created_at' => $date,
                                    'updated_at' => $date,
                                ]
                            );
                            $number = DB::table('works')->where('id', $tmp->id)->increment('browse_number', 1);
                        }
                        unset($tmp->images);
                    }
                }
            } else {
                $list = DB::table('works')
                    ->leftJoin('users', 'works.user_id', '=', 'users.id')
                    ->where('users.school', $users[0]->school)
                    ->where('works.status', 1)
                // ->where('user_id', '!=', $user_id)
                    ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex')
                    ->orderby('id', 'desc')
                    ->skip($page)
                    ->paginate(config('config.page'));
                if (count($list) > 0) {
                    foreach ($list as $tmp) {
                        $tmp->img_path       = explode(',', $tmp->images);
                        $tmp->comments_count = DB::table('comments')->where('works_id', $tmp->id)->where('status', 1)->count();
                        $praise              = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $tmp->id, 1]);
                        if (count($praise) > 0) {
                            $tmp->praise = true;
                        } else {
                            $tmp->praise = false;
                        }
                        $browses = DB::select('select * from browses where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $tmp->user_id, $tmp->id]);
                        if (count($browses) == 0) {
                            $id = DB::table('browses')->insertGetId(
                                [
                                    'user_id'    => $user_id,
                                    'beuser_id'  => $tmp->user_id,
                                    'works_id'   => $tmp->id,
                                    'status'     => 1,
                                    'created_at' => $date,
                                    'updated_at' => $date,
                                ]
                            );
                            $number = DB::table('works')->where('id', $tmp->id)->increment('browse_number', 1);
                        }
                        unset($tmp->images);
                    }
                }
            }
            $list = $list->toArray();
            //官方
            $official = DB::table('users')
                ->leftJoin('works', 'works.user_id', '=', 'users.id')
                ->where('users.official', 1)
                ->where('works.laber_id', $laber_id)
                ->where('works.status', 1)
                ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex', 'users.official')
                ->orderby('id', 'desc')
                ->first();
            if (count($official) > 0) {
                if (!empty($official->images)) {
                    $official->img_path = explode(',', $official->images);
                } else {
                    $official->img_path[0] = null;
                }

                $official->comments_count = DB::table('comments')->where('works_id', $official->id)->where('status', 1)->count();
                if (!empty($user_id) && !empty($token)) {
                    $praise = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $official->id, 1]);
                    if (count($praise) > 0) {
                        $official->praise = true;
                    } else {
                        $official->praise = false;
                    }
                    $browses = DB::select('select * from browses where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $official->user_id, $official->id]);
                    if (count($browses) == 0) {
                        $id = DB::table('browses')->insertGetId(
                            [
                                'user_id'    => $user_id,
                                'beuser_id'  => $official->user_id,
                                'works_id'   => $official->id,
                                'status'     => 1,
                                'created_at' => $date,
                                'updated_at' => $date,
                            ]
                        );
                        $number = DB::table('works')->where('id', $official->id)->increment('browse_number', 1);
                    }
                } else {
                    $official->praise = false;
                }
                unset($official->images);
                array_unshift($list['data'], $official);
            }
        } else {
            $list = $this->getworksdatalist($page, $laber_id, $user_id, $token);
        }
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }

    //选择学校里首页展示全部作品
    public function getselectschoolworkslist(Request $request)
    {
        //参数
        $school_id = $request->input('school_id');
        $user_id   = $request->input('user_id');
        $page      = $request->input('page');
        $token     = $request->input('token');
        $laber_id  = $request->input('laber_id');
        $date      = date('Y-m-d H:i:s');
        if (!empty($user_id) && !empty($token)) {
            //检验用户
            $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
            if (count($users) == 0) {
                return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
            }
        }
        $laber = DB::select('select id,labername from labers where status != :status', ['status' => 0]);
        if ($laber_id != $laber[0]->id) {
            $list = DB::table('works')
                ->leftJoin('users', 'works.user_id', '=', 'users.id')
                ->where('users.school', $school_id)
                ->where('works.laber_id', $laber_id)
                ->where('works.status', 1)
                ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex')
                ->orderby('id', 'desc')
                ->skip($page)
                ->paginate(config('config.page'));
            if (count($list) > 0) {
                foreach ($list as $tmp) {
                    $tmp->img_path       = explode(',', $tmp->images);
                    $tmp->comments_count = DB::table('comments')->where('works_id', $tmp->id)->where('status', 1)->count();
                    if (!empty($user_id) && !empty($token)) {
                        $praise = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $tmp->id, 1]);
                        if (count($praise) > 0) {
                            $tmp->praise = true;
                        } else {
                            $tmp->praise = false;
                        }
                        $browses = DB::select('select * from browses where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $tmp->user_id, $tmp->id]);
                        if (count($browses) == 0) {
                            $id = DB::table('browses')->insertGetId(
                                [
                                    'user_id'    => $user_id,
                                    'beuser_id'  => $tmp->user_id,
                                    'works_id'   => $tmp->id,
                                    'status'     => 1,
                                    'created_at' => $date,
                                    'updated_at' => $date,
                                ]
                            );
                            $number = DB::table('works')->where('id', $tmp->id)->increment('browse_number', 1);
                        }
                    } else {
                        $tmp->praise = false;
                    }
                    unset($tmp->images);
                }
            }
        } else {
            $list = DB::table('works')
                ->leftJoin('users', 'works.user_id', '=', 'users.id')
                ->where('users.school', $school_id)
                ->where('works.status', 1)
                ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex')
                ->orderby('id', 'desc')
                ->skip($page)
                ->paginate(config('config.page'));
            if (count($list) > 0) {
                foreach ($list as $tmp) {
                    $tmp->img_path       = explode(',', $tmp->images);
                    $tmp->comments_count = DB::table('comments')->where('works_id', $tmp->id)->where('status', 1)->count();
                    if (!empty($user_id) && !empty($token)) {
                        $praise = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $tmp->id, 1]);
                        if (count($praise) > 0) {
                            $tmp->praise = true;
                        } else {
                            $tmp->praise = false;
                        }
                        $browses = DB::select('select * from browses where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $tmp->user_id, $tmp->id]);
                        if (count($browses) == 0) {
                            $id = DB::table('browses')->insertGetId(
                                [
                                    'user_id'    => $user_id,
                                    'beuser_id'  => $tmp->user_id,
                                    'works_id'   => $tmp->id,
                                    'status'     => 1,
                                    'created_at' => $date,
                                    'updated_at' => $date,
                                ]
                            );
                            $number = DB::table('works')->where('id', $tmp->id)->increment('browse_number', 1);
                        }
                    } else {
                        $tmp->praise = false;
                    }
                    unset($tmp->images);
                }
            }
        }
        $list = $list->toArray();
        //官方
        $official = DB::table('users')
            ->leftJoin('works', 'works.user_id', '=', 'users.id')
            ->where('users.official', 1)
            ->where('works.laber_id', $laber_id)
            ->where('works.status', 1)
            ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex', 'users.official')
            ->orderby('id', 'desc')
            ->first();
        if (count($official) > 0) {
            if (!empty($official->images)) {
                $official->img_path = explode(',', $official->images);
            } else {
                $official->img_path[0] = null;
            }

            $official->comments_count = DB::table('comments')->where('works_id', $official->id)->where('status', 1)->count();
            if (!empty($user_id) && !empty($token)) {
                $praise = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $official->id, 1]);
                if (count($praise) > 0) {
                    $official->praise = true;
                } else {
                    $official->praise = false;
                }
                $browses = DB::select('select * from browses where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $official->user_id, $official->id]);
                if (count($browses) == 0) {
                    $id = DB::table('browses')->insertGetId(
                        [
                            'user_id'    => $user_id,
                            'beuser_id'  => $official->user_id,
                            'works_id'   => $official->id,
                            'status'     => 1,
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]
                    );
                    $number = DB::table('works')->where('id', $official->id)->increment('browse_number', 1);
                }
            } else {
                $official->praise = false;
            }
            unset($official->images);
            array_unshift($list['data'], $official);
        }
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }

    //未登录首页里展示全部作品
    public function getworkslist(Request $request)
    {
        $page     = $request->input('page');
        $laber_id = $request->input('laber_id');
        $list     = $this->getworksdatalist($page, $laber_id);
        return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $list]);
    }
    //未登录首页里展示全部作品[公共待定义规则]
    public function getworksdatalist($page, $laber_id, $user_id = null, $token = null)
    {
        $date = date('Y-m-d H:i:s');
        if (!empty($user_id) && !empty($token)) {
            //检验用户
            $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
            if (count($users) == 0) {
                return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
            }
        }
        $laber = DB::select('select id,labername from labers where status != :status', ['status' => 0]);
        if ($laber_id != $laber[0]->id) {
            $list = DB::table('works')
                ->leftJoin('users', 'works.user_id', '=', 'users.id')
                ->where('works.laber_id', $laber_id)
                ->where('users.official', 0)
                ->where('works.status', 1)
                ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex', 'users.official')
                ->orderby('id', 'desc')
                ->skip($page)
                ->paginate(config('config.page'));
            if (count($list) > 0) {
                foreach ($list as $tmp) {
                    $tmp->img_path       = explode(',', $tmp->images);
                    $tmp->comments_count = DB::table('comments')->where('works_id', $tmp->id)->where('status', 1)->count();
                    if (!empty($user_id) && !empty($token)) {
                        $praise = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $tmp->id, 1]);
                        if (count($praise) > 0) {
                            $tmp->praise = true;
                        } else {
                            $tmp->praise = false;
                        }
                        $browses = DB::select('select * from browses where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $tmp->user_id, $tmp->id]);
                        if (count($browses) == 0) {
                            $id = DB::table('browses')->insertGetId(
                                [
                                    'user_id'    => $user_id,
                                    'beuser_id'  => $tmp->user_id,
                                    'works_id'   => $tmp->id,
                                    'status'     => 1,
                                    'created_at' => $date,
                                    'updated_at' => $date,
                                ]
                            );
                            $number = DB::table('works')->where('id', $tmp->id)->increment('browse_number', 1);
                        }
                    } else {
                        $tmp->praise = false;
                    }
                    unset($tmp->images);
                }
            }
        } else {
            $list = DB::table('works')
                ->leftJoin('users', 'works.user_id', '=', 'users.id')
                ->where('works.status', 1)
                ->where('users.official', 0)
                ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex', 'users.official')
                ->orderby('id', 'desc')
                ->skip($page)
                ->paginate(config('config.page'));
            if (count($list) > 0) {
                foreach ($list as $tmp) {
                    if (!empty($tmp->images)) {
                        $tmp->img_path = explode(',', $tmp->images);
                    } else {
                        $tmp->img_path[0] = null;
                    }

                    $tmp->comments_count = DB::table('comments')->where('works_id', $tmp->id)->where('status', 1)->count();
                    if (!empty($user_id) && !empty($token)) {
                        $praise = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $tmp->id, 1]);
                        if (count($praise) > 0) {
                            $tmp->praise = true;
                        } else {
                            $tmp->praise = false;
                        }
                        $browses = DB::select('select * from browses where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $tmp->user_id, $tmp->id]);
                        if (count($browses) == 0) {
                            $id = DB::table('browses')->insertGetId(
                                [
                                    'user_id'    => $user_id,
                                    'beuser_id'  => $tmp->user_id,
                                    'works_id'   => $tmp->id,
                                    'status'     => 1,
                                    'created_at' => $date,
                                    'updated_at' => $date,
                                ]
                            );
                            $number = DB::table('works')->where('id', $tmp->id)->increment('browse_number', 1);
                        }
                    } else {
                        $tmp->praise = false;
                    }
                    unset($tmp->images);
                }

            }
        }
        $list = $list->toArray();
        // //官方
        $official = DB::table('users')
            ->leftJoin('works', 'works.user_id', '=', 'users.id')
            ->where('users.official', 1)
            ->where('works.laber_id', $laber_id)
            ->where('works.status', 1)
            ->select('works.*', 'users.nickname', 'users.headportrait', 'users.sex', 'users.official')
            ->orderby('id', 'desc')
            ->first();
        if (count($official) > 0) {
            if (!empty($official->images)) {
                $official->img_path = explode(',', $official->images);
            } else {
                $official->img_path[0] = null;
            }

            $official->comments_count = DB::table('comments')->where('works_id', $official->id)->where('status', 1)->count();
            if (!empty($user_id) && !empty($token)) {
                $praise = DB::select('select * from praises where user_id = ? and works_id = ? and status = ?', [$user_id, $official->id, 1]);
                if (count($praise) > 0) {
                    $official->praise = true;
                } else {
                    $official->praise = false;
                }
                $browses = DB::select('select * from browses where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $official->user_id, $official->id]);
                if (count($browses) == 0) {
                    $id = DB::table('browses')->insertGetId(
                        [
                            'user_id'    => $user_id,
                            'beuser_id'  => $official->user_id,
                            'works_id'   => $official->id,
                            'status'     => 1,
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]
                    );
                    $number = DB::table('works')->where('id', $official->id)->increment('browse_number', 1);
                }
            } else {
                $official->praise = false;
            }
            unset($official->images);
            array_unshift($list['data'], $official);
        }
        return $list;
    }

    //作品点赞
    public function workpraises(Request $request)
    {
        //参数
        $token     = $request->input('token');
        $user_id   = $request->input('user_id');
        $beuser_id = $request->input('beuser_id');
        $works_id  = $request->input('works_id');
        $date      = date('Y-m-d H:i:s');
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        $works = DB::select('select * from works where id = ? and status = ?', [$works_id, 1]);
        if (count($works) == 0) {
            return response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10005'], 'data' => null]);
        }
        $praises = DB::select('select * from praises where user_id = ? and beuser_id = ? and works_id = ?', [$user_id, $beuser_id, $works_id]);

        if (count($praises) > 0) {
            $id = DB::update("update praises set status = 1, updated_at = '{$date}' where user_id = ? and beuser_id = ? and works_id = ?", [$user_id, $beuser_id, $works_id]);
        } else {
            $id = DB::table('praises')->insertGetId(
                [
                    'user_id'    => $user_id,
                    'beuser_id'  => $beuser_id,
                    'works_id'   => $works_id,
                    'status'     => 1,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );
        }
        if ($id > 0) {
            $number        = DB::table('works')->where('id', $works_id)->increment('praise_number', 1);
            $praise_number = DB::select('select praise_number from works where id = :id', ['id' => $works_id]);
            return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $praise_number[0]->praise_number]);
        } else {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10006'], 'data' => null]);
        }
    }

    //取消作品点赞
    public function unworkpraises(Request $request)
    {
        //参数
        $token     = $request->input('token');
        $user_id   = $request->input('user_id');
        $beuser_id = $request->input('beuser_id');
        $works_id  = $request->input('works_id');
        $date      = date('Y-m-d H:i:s');
        //检验用户
        $users = DB::select('select * from users where token = ? and id = ?', [$token, $user_id]);
        if (count($users) == 0) {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10003'], 'data' => null]);
        }
        $works = DB::select('select * from works where id = ? and status = ?', [$works_id, 1]);
        if (count($works) == 0) {
            return response()->json(['status' => 10005, 'msg' => config('config.ErroorCode')['10005'], 'data' => null]);
        }
        $id = DB::update("update praises set status = 0, updated_at = '{$date}' where user_id = ? and beuser_id = ? and works_id = ?", [$user_id, $beuser_id, $works_id]);
        if ($id > 0) {
            DB::table('works')->where('id', $works_id)->decrement('praise_number', 1);
            $praise_number = DB::select('select praise_number from works where id = :id', ['id' => $works_id]);
            return response()->json(['status' => 1, 'msg' => 'ok', 'data' => $praise_number[0]->praise_number]);
        } else {
            return response()->json(['status' => 0, 'msg' => config('config.ErroorCode')['10006'], 'data' => null]);
        }
    }
}

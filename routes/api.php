<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['namespace' => 'Api'], function(){

    //user相关
    //touken校验
    Route::post('/user/checktouken','UserController@checktouken');
    //验证码发送
    Route::post('/user/sendsms','UserController@sendsms');
    //验证码校验
    Route::post('/user/checkphonecode','UserController@checkphonecode');
    //个人信息修改
    Route::post('/user/updateuserinfo','UserController@updateuserinfo');
    //昵称校验
    Route::post('/user/checknickname','UserController@checknickname');
    // work相关
    // work发布
    Route::post('/works/worksadd','WorksController@worksadd');
    //我的作品列表获取
    Route::get('/works/myworkslist','WorksController@myworkslist');
    //我的点赞作品列表获取
    Route::get('/works/mypraisesworkslist','WorksController@mypraisesworkslist');
    //我的作品删除
    Route::get('/works/deletemyworks','WorksController@deletemyworks');
    //首页，未登录里展示全部作品
    Route::get('/works/getworkslist','WorksController@getworkslist');
    //首页，已登录里展示全部作品
    Route::get('/works/getmyschoolworkslist','WorksController@getmyschoolworkslist');
     //首页，选择学校里展示全部作品
    Route::get('/works/getselectschoolworkslist','WorksController@getselectschoolworkslist');
     // 点赞
    Route::post('/works/workpraises','WorksController@workpraises');
     // 取消点赞
    Route::post('/works/unworkpraises','WorksController@unworkpraises');
    // school相关
    //学校列表获取
    Route::get('/school/getschoollist','SchoolController@getschoollist');
    // laber相关
    //版块标签列表获取(首页)
    Route::get('/laber/gethomelaberlist','LabersController@gethomelaberlist');
    //版块标签列表获取(发布)
    Route::get('/laber/getaddlaberlist','LabersController@getaddlaberlist');
    //comments相关
     //comments评论列表
    Route::get('/comments/getcommentslist','CommentsController@getcommentslist');
    //comments发布
    Route::post('/comments/addcomments','CommentsController@addcomments');
    //replys相关
    //replys回复列表
    Route::get('/replys/getreplyslist','ReplysController@getreplyslist');
    //replys发布
    Route::post('/replys/addreplys','ReplysController@addreplys');

    //测试
    Route::get('/user/osstest','UserController@osstest');
});
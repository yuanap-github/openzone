<?php

namespace App\Admin\Controllers;

use App\Models\Works;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WorksController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Works';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Works);

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('用户ID'));
        $grid->column('content', __('内容'));
        // $grid->column('images', __('Images'));
        // 显示多图
        // $grid->profile_photo('头像')->image(30,30);
        // $grid->images('图片')->display(function ($images) {
        //   $tmp = explode(',', $images);
        //   return $grid->images('头像')->image(30,30);
        // });



        $grid->column('praise_number', __('点赞数'));
        $grid->column('browse_number', __('播放数'));
        $grid->column('created_at', __('发布时间'));
        $grid->column('updated_at', __('更新时间'));
        $grid->column('school_id', __('学校'));
        $grid->status('是否删除')->display(function ($status) {
         if($status == 1){
             return '正常';
         }else{
             return '已删除';
         }
        });
        $grid->column('laber_id', __('标签版块'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Works::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('content', __('Content'));
        $show->field('images', __('Images'));
        $show->field('praise_number', __('Praise number'));
        $show->field('browse_number', __('Browse number'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('school_id', __('School id'));
        $show->field('status', __('Status'));
        $show->field('laber_id', __('Laber id'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Works);

        $form->number('user_id', __('User id'));
        $form->text('content', __('Content'));
        $form->text('images', __('Images'));
        $form->number('praise_number', __('Praise number'));
        $form->number('browse_number', __('Browse number'));
        $form->number('school_id', __('School id'));
        $form->number('status', __('Status'))->default(1);
        $form->number('laber_id', __('Laber id'));

        return $form;
    }
}

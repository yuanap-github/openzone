<?php

namespace App\Admin\Controllers;

use App\Models\School;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SchoolController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '学校列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new School);

        $grid->column('id', __('Id'));
        $grid->column('schoolname', __('学校名称'));
        $grid->column('province', __('归属省会'));
        $grid->column('status', __('Status'));
        $grid->status('是否开启')->display(function ($status) {
         if($status == 1){
             return '已开启';
         }else{
             return '未开启';
         }
        });
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'));

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
        $show = new Show(School::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('schoolname', __('学校名称'));
        $show->field('province', __('省份'));
        $show->status('是否开启')->display(function ($status) {
         if($status == 1){
             return '已开启';
         }else{
             return '未开启';
         }
        });
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('更新时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new School);

        $form->text('schoolname', __('学校名称'));
        $form->select('province','省份')->options(['ah' => '安徽', 'sc' => '四川', 'sd' => '山东']);
        $form->radio('status','状态')->options(['1' => '开启', '0'=> '关闭'])->default('1');
        return $form;
    }
}

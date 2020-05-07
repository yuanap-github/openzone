<?php

namespace App\Admin\Controllers;

use App\Models\Labers;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LabersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Labers';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Labers);

        $grid->column('id', __('Id'));
        $grid->column('labername', __('版块名称'));
        $grid->status('是否开启')->display(function ($status) {
          if($status == 1){
             return '已开启';
         }else if ($status == 2) {
             return '特殊';
         }else{
             return '未开启';
         }
         });
        // $grid ->column('introduce', __('介绍'));
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
        $show = new Show(Labers::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('labername', __('版块名称'));
        $show->status('是否开启')->display(function ($status) {
         if($status == 1){
             return '已开启';
         }else if ($status == 2) {
             return '特殊';
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
        $form = new Form(new Labers);

        $form->text('labername', __('版块名称'));
        $form->text('introduce', __('介绍'));
        $form->radio('status','状态')->options(['1' => '开启', '0'=> '关闭', '2'=> '特殊'])->default('1');

        return $form;
    }
}

<?php

namespace App\Admin\Controllers;

use App\Models\Users;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Users';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Users);

        $grid->column('id', __('Id'));
        $grid->column('nickname', __('Nickname'));
        $grid->headportrait('头像')->image(30,30);
        $grid->sex('性别')->display(function ($sex) {
          if($sex == 0){
            return '';
          } else if($sex==1){
            return '男';
          }else{
            return '女';
          }
        });
        $grid->column('phone', __('手机号'));
        $grid->column('school', __('学校'));
        $grid->column('token', __('Token'));
        $grid->status('状态')->display(function ($status) {
          if($status == 1){
            return '正常';
          }else{
            return '已封号';
          }
        });
         $grid->official('类型')->display(function ($official) {
          if($official == 1){
            return "<span style='color:red;font-weight: bold;'>官方</span>";
          }else{
            return '普通';
          }
        });
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->column('created_at', __('注册时间'));
        $grid->column('updated_at', __('登录时间'));

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
        $show = new Show(Users::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('nickname', __('Nickname'));
        $show->field('headportrait', __('Headportrait'));
        $show->field('phone', __('Phone'));
        $show->field('school', __('School'));
        $show->field('token', __('Token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('sex', __('Sex'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Users);

        $form->text('nickname', __('Nickname'));
        $form->text('headportrait', __('Headportrait'));
        $form->mobile('phone', __('Phone'));
        $form->text('school', __('School'));
        $form->radio('status','账号状态')->options(['1' => '正常', '0'=> '封号'])->default('1');
        $form->text('token', __('Token'));
        $form->number('sex', __('Sex'));

        return $form;
    }
}

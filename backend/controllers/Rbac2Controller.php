<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 9:04
 */

namespace backend\controllers;


use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class Rbac2Controller extends Controller
{
    /*
     * 权限添加
     */
    public function actionAddPermission()
    {
        // >1 显示表单（实例化模型，ActiveFrom组件渲染表单）
        //>1.1 实例化模型（表单模型，活动记录）
        $model = new PermissionForm();

        //>2 接收表单数据 //>3 验证表单数据
        if($model->load(\Yii::$app->request->post()) && $model->validate())
        {
            //>4 保存
            if($model->add()){
                \Yii::$app->session->setFlash('success','权限添加成功');
                return $this->redirect(['rbac/permission-index']);
            }
        }
        //数据验证不通过，将错误信息显示到表单
        //>1.2 调用视图显示表单
        return $this->render('/rbac/addPermission',['model'=>$model]);

    }

    /*
     * 添加角色
     */
    public function actionAddRole()
    {
        // >1 显示表单（实例化模型，ActiveFrom组件渲染表单）
        //>1.1 实例化模型（表单模型，活动记录）
        $model = new RoleForm();
        $model->scenario = RoleForm::SCENARIO_ADD;
        //>2 接收表单数据 //>3 验证表单数据
        if($model->load(\Yii::$app->request->post()) && $model->validate())
        {
            //var_dump($model->permissions);exit;
            //>4 保存
            if($model->save()){
                \Yii::$app->session->setFlash('success','权限添加成功');
                return $this->redirect(['rbac/role-index']);
            }
        }
        //数据验证不通过，将错误信息显示到表单
        //>1.2 调用视图显示表单
        return $this->render('/rbac/addRole',['model'=>$model]);

    }

    /*
     * 修改角色
     */
    public function actionEditRole($name)
    {
        // >1 显示表单（实例化模型，ActiveFrom组件渲染表单）
        //>1.1 实例化模型（表单模型，活动记录）
        $model = new RoleForm();
        //根据角色名获取角色
        $role = \Yii::$app->authManager->getRole($name);
        if($role==null){
            throw new NotFoundHttpException('角色不存在');
        }
        $permissions = \Yii::$app->authManager->getPermissionsByRole($role->name);
        //var_dump($permissions);exit;
        //加载角色数据到表单（数据回显）
        $model->loadFromRole($role);
        //>2 接收表单数据 //>3 验证表单数据
        if($model->load(\Yii::$app->request->post()) && $model->validate())
        {
            //var_dump($model->permissions);exit;
            //>4 保存
            if($model->save($role)){
                \Yii::$app->session->setFlash('success','权限修改成功');
                return $this->redirect(['rbac/role-index']);
            }
        }
        //数据验证不通过，将错误信息显示到表单
        //>1.2 调用视图显示表单
        return $this->render('/rbac/addRole',['model'=>$model]);

    }
}
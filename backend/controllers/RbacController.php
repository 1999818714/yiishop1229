<?php

namespace backend\controllers;

use backend\models\PermissionForm;
use backend\models\RoleForm;

class RbacController extends \yii\web\Controller
{
    /*
     * 权限列表
     */
    public function actionPermissionIndex()
    {
        //获取所有权限
        $authManager = \Yii::$app->authManager;
        $permissions = $authManager->getPermissions();

        return $this->render('permissionIndex',['permissions'=>$permissions]);
    }


    /*
     * 添加权限
     */
    public function actionAddPermission()
    {
       /*
       回顾RBAC添加权限的方法
       $authManager = \Yii::$app->authManager;
        //1 添加user/add权限
        $permission = $authManager->createPermission('user/add');//创建权限
        $authManager->add($permission);//添加到数据表
        */
        $model = new PermissionForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $authManager = \Yii::$app->authManager;
            //$authManager->getPermission();//获取权限
            //1 添加user/add权限
            $permission = $authManager->createPermission($model->name);//创建权限
            $permission->description = $model->description;
            //添加到数据表
            if($authManager->add($permission)){
                //权限添加成功
                \Yii::$app->session->setFlash('success',$permission->description.' 权限添加成功');
                return $this->redirect(['rbac/permission-index']);
            }
        }
        return $this->render('addPermission',['model'=>$model]);

    }
    /*
     * 删除权限
     */
    public function actionDelPermission($name){
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($name);
        $authManager->remove($permission);
    }

    /*
     * 角色列表
     */
    public function actionRoleIndex()
    {
        $authManager = \Yii::$app->authManager;
        $roles = $authManager->getRoles();

        return $this->render('roleIndex',['roles'=>$roles]);
    }
    /*
     * 添加角色
     */
    public function actionAddRole()
    {
        $model = new RoleForm();
        //指定场景
        $model->scenario = RoleForm::SCENARIO_ADD;
        //给角色添加权限
        /*$authManager = \Yii::$app->authManager;
        $role = $authManager->createRole('admin');
        $permission = $authManager->getPermission('goods/add');
        $authManager->addChild($role,$permission);*/
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $authManager = \Yii::$app->authManager;
            $role = $authManager->createRole($model->name);//创建角色
            $role->description = $model->description;
            $authManager->add($role);//添加到数据表
            //给角色关联权限
            foreach($model->permissions as $permission){
                $authManager->addChild($role,$authManager->getPermission($permission));
            }
            \Yii::$app->session->setFlash('success',$role->name.' 角色添加成功');
            return $this->redirect(['rbac/role-index']);
        }
        return $this->render('addRole',['model'=>$model]);
    }

    /*
     * 修改角色
     */
    public function actionEditRole($name)
    {
        $model = new RoleForm();
        $authManager = \Yii::$app->authManager;
        //获取要修改的角色
        $role = $authManager->getRole($name);

        $model->name = $role->name;
        $model->description = $role->description;
        $permissions = $authManager->getPermissionsByRole($role->name);
        //var_dump($permissions);exit;
        $model->permissions = array_keys($permissions);
        //var_dump($model->permissions);exit;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){

            //$role->name = $model->name;
            $role->description = $model->description;
            $authManager->update($role->name,$role);//更新到数据表
            //给角色关联权限
            //先清除之前关联的所有权限
            $authManager->removeChildren($role);
            foreach($model->permissions as $permission){

                $authManager->addChild($role,$authManager->getPermission($permission));
            }
            \Yii::$app->session->setFlash('success',$role->name.' 角色更新成功');
            return $this->redirect(['rbac/role-index']);
        }
        return $this->render('addRole',['model'=>$model]);
    }

    /*
     * 删除角色
     */
    public function actionDelRole($name)
    {
        $role = \Yii::$app->authManager->getRole($name);

        \Yii::$app->authManager->remove($role);
    }


    /*
     * 用户添加角色
     */
    public function actionTest()
    {
        $authManager = \Yii::$app->authManager;
        //给id=1的用户添加admin角色
        $role = $authManager->getRole('admin');
        $authManager->assign($role,1);

        //修改角色
        //清除所有id=1的用户关联角色
        $authManager->revokeAll(1);


    }
}

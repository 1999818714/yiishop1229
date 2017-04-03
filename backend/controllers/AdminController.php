<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/3
 * Time: 11:18
 */

namespace backend\controllers;


use backend\models\Admin;
use yii\web\Controller;

class AdminController extends Controller
{

    /*
     * 添加管理员
     */
    public function actionAdd()
    {

        $admin = new Admin();
        $admin->username = 'admin';
        $admin->password_hash = '123456';
        $admin->password_hash = \Yii::$app->security->generatePasswordHash($admin->password_hash);
        $admin->email = 'admin@admin.com';
        $admin->auth_key = \Yii::$app->security->generateRandomString();
        $admin->created_at = time();
        $admin->save();
        //注册完成后自动帮用户登录账号
        \Yii::$app->user->login($admin);
    }

    public function actionLogin()
    {
        $model = new Admin();
        //指定场景
        $model->scenario = Admin::SCENARIO_LOGIN;
        if($model->load(\Yii::$app->request->post())) {
            if($model->validate()){
                //echo '验证成功';exit;
                $admin = Admin::findOne(['id'=>1]);
                \Yii::$app->user->login($admin,3600*24*7);
                return $this->redirect(['admin/user']);
            }else{
                //var_dump($model->getErrors());exit;
            }
        }
        return $this->render('login',['model'=>$model]);
    }

    public function actionUser()
    {
        var_dump(\Yii::$app->user->isGuest);

    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['admin/login']);
    }
}
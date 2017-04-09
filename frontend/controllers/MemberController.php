<?php

namespace frontend\controllers;

use frontend\models\Member;

class MemberController extends \yii\web\Controller
{
    public $layout = 'login';//指定布局文件
    /*public function actionIndex()
    {
        return $this->render('index');
    }*/
    /*
     * 用户注册
     */
    public function actionRegister()
    {
        $model = new Member();


        return $this->render('register',['model'=>$model]);
    }

}

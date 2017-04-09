<?php

namespace frontend\controllers;

use frontend\models\Member;
use yii\filters\AccessControl;

class MemberController extends \yii\web\Controller
{
    public $layout = 'login';//指定布局文件

    public function behaviors()
    {
        return [
            'access'=>[
                'class'=>AccessControl::className(),
                'only'=>['address'],
                'rules'=>[
                    [
                        'allow'=>true,
                        'actions'=>['address'],
                        'roles'=>['@']
                    ]
                ]
            ]
        ];
    }


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
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();

            //\Yii::$app->session->setFlash('success','注册成功');
            return $this->redirect(['member/login']);
        }

        return $this->render('register',['model'=>$model]);
    }

    /*
     * 用户登录
     */
    public function actionLogin(){
        $member = Member::findOne(['id'=>1]);
        \Yii::$app->user->login($member);
        return $this->redirect(['member/address']);
    }

    /*
     * 发送短信
     */
    public function actionSms()
    {

    }


    /*
     * 地址管理
     */
    public function actionAddress()
    {
        $this->layout = 'member';
        return $this->render('address');
    }
}

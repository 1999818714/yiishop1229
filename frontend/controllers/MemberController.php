<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Member;
use yii\filters\AccessControl;

use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Flc\Alidayu\Requests\IRequest;

class MemberController extends \yii\web\Controller
{
    public $layout = 'login';//指定布局文件

    //public $enableCsrfValidation = false;
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
        /*
         * App Key:23746954
         * App Secret: d53357cf11abd2c8d53fc0ac08eecf04
         */

        //生成短信验证码  手机号码
        $tel = \Yii::$app->request->post('tel');
        //echo $tel;
        $code = rand(1000,9999);//随机生成短信验证码
        \Yii::$app->session->set('tel_'.$tel,$code);
        //发送短信验证码到手机

        return [
            'err_code'=>0,
            'msg'=>'短信发送成功'
        ];

    }

    /*
     * 测试发送短信
     */
    public function actionTest()
    {


// 配置信息
        $config = [
            'app_key'    => '23746954',
            'app_secret' => 'd53357cf11abd2c8d53fc0ac08eecf04',
            // 'sandbox'    => true,  // 是否为沙箱环境，默认false
        ];


// 使用方法一
        $client = new Client(new App($config));
        $req    = new AlibabaAliqinFcSmsNumSend;

        $req->setRecNum('13890021537')//要发送给谁 电话号码
            ->setSmsParam([
                'content' => rand(100000, 999999)//设置参数，根短信模板上的参数名一致
            ])
            ->setSmsFreeSignName('季老师')//签名，必须要设置 签名必须是已审核的
            ->setSmsTemplateCode('SMS_51035095');//短信模板ID

        $resp = $client->execute($req);
        var_dump($resp);
    }


    /*
     * 地址管理
     */
    public function actionAddress()
    {
        $this->layout = 'member';
        $model = new Address();


        return $this->render('address',['model'=>$model]);
    }
}

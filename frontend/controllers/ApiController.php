<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 10:15
 */

namespace frontend\controllers;


use frontend\models\Member;
use yii\helpers\Json;
use yii\web\Controller;

class ApiController extends Controller
{
    public function actionLogin()
    {
        //username=test&pwd=123456
        //echo 'login';
        $result = [
            "success"=>false,
            "errorMsg"=> "",
            "result"=>[]
        ];
        $username = \Yii::$app->request->get('username');
        $pwd = \Yii::$app->request->get('pwd');
        $member = Member::findOne(['username'=>$username]);
        if($member){
            if(\Yii::$app->security->validatePassword($pwd,$member->password_hash)){
                //登录成功
                \Yii::$app->user->login($member);
                $result['success']=true;
                $result['result']=[
                    "id"=>$member->id,
                   "userName"=>$member->username,
                   "userIcon"=> "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1492493251900&di=b79d91e94b5be83ee6999ee6a1dbd59c&imgtype=0&src=http%3A%2F%2Fwww.qq1234.org%2Fuploads%2Fallimg%2F150618%2F8_150618144141_5.jpg",
                   "waitPayCount"=>1,
                   "waitReceiveCount"=> 2,
                   "userLevel"=> 5
                ];
                return Json::encode($result);
            }
        }
        $result['errorMsg']='登录失败，用户名或密码错误';
        return Json::encode($result);
    }

    public function actionBanner()
    {
        //adKind=1  adKind=2
        $adKind = \Yii::$app->request->get('adKind');
//        echo 'actionBanner';
        $result = [
            "success"=>true,
            "errorMsg"=> "",
            "result"=>[
                 [
                     "id"=>1,
                       "type"=> 1,
                       "adUrl"=> "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1492493251900&di=826d87c04785f0c12daa5c9b457a27ee&imgtype=0&src=http%3A%2F%2Fb.hiphotos.baidu.com%2Fzhidao%2Fwh%253D450%252C600%2Fsign%3Df0c5c08030d3d539c16807c70fb7c566%2F8ad4b31c8701a18bbef9f231982f07082838feba.jpg",
                       "webUrl"=>"http://www.baidu.com",
                       "adKind"=> $adKind
                  ]
            ]
        ];

        return Json::encode($result);


    }
    public function actionSeckill()
    {
        $result = [
            "success"=>true,
            "errorMsg"=> "",
            "result"=>[
                "total"=> 2,
                  "rows"=> [
                        [
                            "allPrice"=>999,
                          "pointPrice"=>998,
                          "iconUrl"=> "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1492493251900&di=db73af248edcb9afb035dffee2ba1273&imgtype=0&src=http%3A%2F%2Fimg.cnjiayu.net%2F3211573049-3310678237-21-0.jpg",
                          "timeLeft"=> 99,
                          "type"=> 2,
                          "productId"=>1
                        ],
                      [
                          "allPrice"=>999,
                          "pointPrice"=>998,
                          "iconUrl"=> "/../images/crazy1.jpg",
                          "timeLeft"=> 99,
                          "type"=> 2,
                          "productId"=>7
                      ]
                  ]
            ]
        ];

        return Json::encode($result);
    }

    public function actionGetYourFav()
    {
        $result = [
            "success"=>true,
            "errorMsg"=> "",
            "result"=>[
                "total"=> 1,
                "rows"=> [
                    [
                        "price"=>1998,
                       "name"=> "小米",
                       "iconUrl"=> "/../images/crazy1.jpg",
                       "productId"=>2
                    ]
                ]
            ]
        ];

        return Json::encode($result);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/11
 * Time: 15:24
 */

namespace frontend\controllers;


use yii\web\Controller;

class IndexController extends Controller
{

    /*
     * 首页
     */
    public function actionIndex()
    {

        return $this->render('index');
    }

    /*
     * 列表
     */
    public function actionList()
    {

    }
    /*
     * 商品详情
     */
    public function actionDetail($sn)
    {

    }

    /*
     * 发送邮件
     */
    public function actionMail()
    {
        $r = \Yii::$app->mailer->compose()
            ->setFrom('quan0125bin@163.com')
            ->setTo('quan0125bin@163.com')
            ->setSubject('随便发发2')
            ->setTextBody('阿里云服务器不得不知道的禁忌2')
            ->setHtmlBody('<b>阿里云服务器不得不知道的禁忌2</b>')
            ->send();
        var_dump($r);
    }
}
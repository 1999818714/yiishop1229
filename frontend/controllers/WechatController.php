<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/22
 * Time: 11:02
 */

namespace frontend\controllers;


use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use frontend\models\Member;
use yii\helpers\Url;
use yii\web\Controller;
use EasyWeChat\Foundation\Application;

/*
 * 1、菜单设置好，点击 美女排行榜 按钮，回复美女排行榜图文信息（注意修改账号基本信息【配置文件里面修改】）
 * 2、在页面获取用户openid（配置授权回调地址【配置文件里面修改】，修改授权回调域名【在测试号后台修改】）
 */

class WechatController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionIndex()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $server = $app->server;
        $server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    switch ($message->Event) {
                        case 'subscribe':
                            # code...
                            break;
                        case 'CLICK'://自定义菜单点击事件
                            //根据key值判断点击了哪个按钮
                            return $message->EventKey;
                            break;
                        default:
                            # code...
                            break;
                    }
                    return '收到事件消息';
                    break;
                case 'text':
                    if($message->Content == '美女排行榜'){
                        $articles = [
//    ['title'=>'','Description'=>'','PicUrl'=>'http://mei.hercity.com/data/upfiles/thumb/2011/12/20111202142224263950.jpg','Url'=>''],
                            ['title'=>'','Description'=>'','PicUrl'=>'http://mei.hercity.com/data/upfiles/thumb/2013/04/20130423090055953093.jpg','Url'=>''],
                            ['title'=>'','Description'=>'','PicUrl'=>'http://mei.hercity.com/data/upfiles/thumb/2011/11/20111114224745296423.jpg','Url'=>''],
                            ['title'=>'','Description'=>'','PicUrl'=>'http://mei.hercity.com/data/upfiles/thumb/2014/12/20141225170742418902.jpg','Url'=>''],
                            ['title'=>'','Description'=>'','PicUrl'=>'http://mei.hercity.com/data/upfiles/thumb/2013/05/20130501145311588608.jpg','Url'=>''],
                            ['title'=>'','Description'=>'','PicUrl'=>'http://mei.hercity.com/data/upfiles/thumb/2014/12/20141225171119990603.jpg','Url'=>''],
                            ['title'=>'','Description'=>'','PicUrl'=>'http://mei.hercity.com/data/upfiles/thumb/2014/12/20141225144832960024.jpg','Url'=>''],
                            ['title'=>'','Description'=>'','PicUrl'=>'http://mei.hercity.com/data/upfiles/thumb/2014/10/20141027222213817061.jpg','Url'=>''],
                            ['title'=>'','Description'=>'','PicUrl'=>'http://mei.hercity.com/data/upfiles/thumb/2011/11/20111101161958723510.jpg','Url'=>''],
                        ];
                        $result = [];
                        foreach($articles as $article){
                            $news = new News([
                                'title'       => $article['title'],
                                'description' => $article['Description'],
                                'url'         => $article['Url'],
                                'image'       => $article['PicUrl'],
                            ]);
                            $result[] = $news;
                        }
                        return $result;
                        /*$news1 = new News(...);
                        $news2 = new News(...);
                        $news3 = new News(...);
                        $news4 = new News(...);
                        return [$news1, $news2, $news3, $news4];*/
                    }elseif($message->Content == '帮助'){
//                        return new Text(['content' => '帮助信息']);
                        return '帮助信息';
                    }

                    break;
                /*case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;*/
            }
            // ...
        });


        $response = $server->serve();
        // 将响应输出
        $response->send(); // Laravel 里请使用：return $response;

        //return $_GET['echostr'];服务器验证最简单的方法
    }

    //查询菜单
    public function actionGetMenus()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $menu = $app->menu;
        $menus = $menu->all();
        var_dump($menus);
    }

    //设置菜单
    public function actionSetMenus()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $menu = $app->menu;
        $buttons = [
            [
                "type" => "click",
                "name" => "今日歌曲",
                "key"  => "V1001_TODAY_MUSIC"
            ],
            [
                "name"       => "个人信息",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "账户信息",
                        "url"  => Url::to(['wechat/user'],true),
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://v.qq.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];
        $r = $menu->add($buttons);
        var_dump($r);
    }

    //个人账户信息
    public function actionUser()
    {
        //检查session中是否有openid
        //如果没有
        if(!\Yii::$app->session->get('openid')){
            //获取用户的openid
            //echo 'user';
            $app = new Application(\Yii::$app->params['wechat']);
            $response = $app->oauth->redirect();
            //将当前路由保存到session，便于授权回调地址跳回当前页面
            \Yii::$app->session->setFlash('back','wechat/user');
            $response->send();
        }
        //从session中获取openid
        $openid = \Yii::$app->session->get('openid');
        //查询该openid是否绑定账号
        $member = Member::findOne(['openid'=>$openid]);
        if($member == null){
            //没有绑定，跳转到绑定页面
            return $this->redirect(['wechat/bang']);
        }
        //显示当前用户的账号信息
        var_dump($member);

    }
    //查询个人订单
    public function actionOrders()
    {

    }

    //网页授权回调地址
    public function actionCallback()
    {
        $app = new Application(\Yii::$app->params['wechat']);
//        echo 'callback';
        $user = $app->oauth->user();
        //用户的openid
        $user->id;
        //将用户的openid保存到session
        \Yii::$app->session->set('openid',$user->id);

        //跳回请求地址
        if(\Yii::$app->session->hasFlash('back')){
            return $this->redirect([\Yii::$app->session->getFlash('back')]);
        }

    }

    //绑定账号
    public function actionBang()
    {

    }

    //解除绑定
    public function actionUnlink()
    {

    }


}
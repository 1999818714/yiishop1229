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
use frontend\models\BangForm;
use frontend\models\Member;
use frontend\models\Order;
use yii\helpers\Url;
use yii\web\Controller;
use EasyWeChat\Foundation\Application;
use yii\web\HttpException;

/*
 * 1、菜单设置好，点击 美女排行榜 按钮，回复美女排行榜图文信息（注意修改账号基本信息【配置文件里面修改】）
 * 2、在页面获取用户openid（配置授权回调地址【配置文件里面修改】，修改授权回调域名【在测试号后台修改】）
 */

class WechatController extends Controller
{
    public $enableCsrfValidation = false;//微信开发必须关闭csrf验证
    public $layout = 'main';//微信页面使用bootstrap样式
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
                "name" => "热卖商品",
                "key"  => "hot_goods"
            ],
            [
                "name"       => "个人中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "我的信息",
                        "url"  => Url::to(['wechat/user'],true),
                    ],
                    [
                        "type" => "view",
                        "name" => "我的订单",
                        "url"  => Url::to(['wechat/orders'],true),
                    ],
                    [
                        "type" => "view",
                        "name" => "绑定账号",
                        "url"  => Url::to(['wechat/bang'],true),
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
        if(\Yii::$app->user->isGuest){
            \Yii::$app->session->setFlash('return','wechat/user');
            return $this->redirect(['wechat/bang']);
        }
        /*//检查session中是否有openid
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
        }*/
        //显示当前用户的账号信息
        var_dump(\Yii::$app->user->identity);

    }
    //查询个人订单
    public function actionOrders()
    {
        if(\Yii::$app->user->isGuest){
            \Yii::$app->session->setFlash('return','wechat/orders');
            return $this->redirect(['wechat/bang']);
        }
        $orders = Order::find()->where(['member_id'=>\Yii::$app->user->id])->all();
        var_dump($orders);
    }

    //网页授权回调地址
    public function actionCallback()
    {
        $app = new Application(\Yii::$app->params['wechat']);
//        echo 'callback';
        $user = $app->oauth->user();
        //用户的openid
        //$user->id;
        //将用户的openid保存到session
        \Yii::$app->session->set('openid',$user->id);

        //跳回请求地址
        if(\Yii::$app->session->hasFlash('back')){
            return $this->redirect([\Yii::$app->session->getFlash('back')]);
        }else{
//            return $this->redirect(['wechat/bang']);
            var_dump(\Yii::$app->session->getFlash('back',null,true));
        }

    }

    //绑定账号
    public function actionBang()
    {
        if(!\Yii::$app->session->get('openid')){
            //获取用户的openid
            //echo 'user';
            $app = new Application(\Yii::$app->params['wechat']);
            \Yii::$app->session->setFlash('back','wechat/bang');
            $response = $app->oauth->redirect();
            //将当前路由保存到session，便于授权回调地址跳回当前页面

            $response->send();
        }
        //从session中获取openid
        $openid= \Yii::$app->session->get('openid');
        if($openid==null){
            throw new HttpException(404,'未获取到用户信息');
        }

        $member = Member::findOne(['openid'=>$openid]);
        if($member){//如果已绑定账号，则显示解绑按钮

            //if(\Yii::$app->user->isGuest){
                \Yii::$app->user->login($member);//使用openid自动登录
//            }

            if(\Yii::$app->session->hasFlash('return')){
                return $this->redirect([\Yii::$app->session->getFlash('return')]);
            }
            return $this->render('unlink');
        }
        //如果未绑定账号，则显示绑定表单
        $model = new BangForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $member = Member::findOne(['username'=>$model->username]);
            if($member){

                if(\Yii::$app->security->validatePassword($model->password,$member->password_hash)){
                    \Yii::$app->user->login($member);
                    $member->updateAttributes(['openid'=>$openid]);//绑定openid

                    //\Yii::$app->session->setFlash('success','账号绑定成功');

                    if(\Yii::$app->session->hasFlash('return')){
                            return $this->redirect([\Yii::$app->session->getFlash('return')]);
                    }
                    return $this->refresh();
                }
            }
            $model->addError('username','账号或密码不正确');
        }
        return  $this->render('bang',['model'=>$model]);

    }

    //解除绑定
    public function actionUnlink()
    {
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['wechat/bang']);
        }
        //$member = Member::findOne(['openid'=>$openid]);
        \Yii::$app->user->identity->updateAttributes(['openid'=>null]);


        \Yii::$app->user->logout();
        //\Yii::$app->session->destroy();

        \Yii::$app->session->setFlash('success','解除绑定成功');
        return $this->redirect(['wechat/bang']);
    }








}
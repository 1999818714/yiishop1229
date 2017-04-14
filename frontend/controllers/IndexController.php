<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/11
 * Time: 15:24
 */

namespace frontend\controllers;


use backend\models\Goods;
use frontend\components\CookieHandler;
use yii\web\Controller;
use yii\web\Cookie;

class IndexController extends Controller
{
    public $enableCsrfValidation = false;
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
    /*public function actionMail()
    {
        $r = \Yii::$app->mailer->compose()
            ->setFrom('quan0125bin@163.com')
            ->setTo('quan0125bin@163.com')
            ->setSubject('随便发发2')
            ->setTextBody('阿里云服务器不得不知道的禁忌2')
            ->setHtmlBody('<b>阿里云服务器不得不知道的禁忌2</b>')
            ->send();
        var_dump($r);
    }*/



    /*
     * 添加到购车车提示页
     */
    public function actionNotice($goods_id,$num=1)
    {
        if(\Yii::$app->user->isGuest){
            //将购物车的数据取出
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie == null){//购物车cookie不存在
                $cart = [];
            }else{//购物车cookie存在
                $cart = unserialize($cookie->value);
            }
            //将商品id和数量保存到cookie  //array_key_exists()检查数组中是否有给定键名
            //if(isset($cart[$goods_id]))
            if(array_key_exists($goods_id,$cart)){
                //2 购物车已经有该商品  数量累加
                $cart[$goods_id] += $num;
            }else{
                //1 购物车没有该商品   直接添加到数组
                $cart[$goods_id] = $num;
            }

            //将购车数据保存回cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($cart)
            ]);

            $cookies->add($cookie);
        }else{
            //1 检查购物车有没有该商品(根据goods_id member_id查询)
            //1.1 有该商品  数量累加
            //1.2 没有该商品  添加到数据表

        }


        //直接跳转到购物车
        return $this->redirect(['index/cart']);
    }


    /*
     * 购物车
     */
    public function actionCart()
    {
        $this->layout = 'cart';
        if(\Yii::$app->user->isGuest){
            //将商品id和数量从cookie取出
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie == null){//购物车cookie不存在
                $cart = [];
            }else{//购物车cookie存在
                $cart = unserialize($cookie->value);
            }
        }else{
            //用户已登录，从数据表获取购物车数据
            $cart = Cart::find()->where(['member_id'=>\Yii::$app->user->id])->asArray()->all();
        }
        //$cart;//[1=>2,2=>9]
        //var_dump($cart);
        $models = [];//[[id=>1,logo=>'',name=>'',price=>'','num'=>2],[]]
        //循环获取商品数据，构造购物车需要的格式
        foreach($cart as $id=>$num){
            $goods = Goods::find()->where(['id'=>$id])->asArray()->one();
            $goods['num']=$num;
            $models[]=$goods;
        }
//        var_dump($models);exit;

        return $this->render('cart',['models'=>$models]);
    }

    /*
     * 修改购物车商品数量
     * $filter = modify   del
     */
    public function actionAjax($filter)
    {
        switch ($filter){
            case 'modify':
                //修改商品数量 goods_id  num
                $goods_id = \Yii::$app->request->post('goods_id');
                $num = \Yii::$app->request->post('num');

                if(\Yii::$app->user->isGuest){
                    /*$cookies = \Yii::$app->request->cookies;
                    $cookie = $cookies->get('cart');
                    if($cookie == null){//购物车cookie不存在
                        $cart = [];
                    }else{//购物车cookie存在
                        $cart = unserialize($cookie->value);
                    }

                    $cart[$goods_id] = $num;
                    //将购车数据保存回cookie
                    $cookies = \Yii::$app->response->cookies;
                    $cookie = new Cookie([
                        'name'=>'cart',
                        'value'=>serialize($cart)
                    ]);

                    $cookies->add($cookie);*/
                    \Yii::$app->cartCookie->updateCart($goods_id,$num)->save();
                }
                return 'success';
                break;

            case 'del':
                //删除商品
                $goods_id = \Yii::$app->request->post('goods_id');

                if(\Yii::$app->user->isGuest){
                    /*$cookies = \Yii::$app->request->cookies;
                    $cookie = $cookies->get('cart');
                    if($cookie == null){//购物车cookie不存在
                        $cart = [];
                    }else{//购物车cookie存在
                        $cart = unserialize($cookie->value);
                    }
                    //清除购物车中该id对应的商品
                    unset($cart[$goods_id]);
                    //将购车数据保存回cookie
                    $cookies = \Yii::$app->response->cookies;
                    $cookie = new Cookie([
                        'name'=>'cart',
                        'value'=>serialize($cart)
                    ]);

                    $cookies->add($cookie);*/
                   /* $cart = new CookieHandler();
                    $cart->delCart($goods_id);
                    $cart->save();*/
                    \Yii::$app->cartCookie->delCart($goods_id)->save();
                    return 'success';
                }
                break;
        }


    }
}
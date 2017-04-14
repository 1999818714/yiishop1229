<?php

namespace frontend\components;
/*
*操作cookie里面的购物车数据
*/
use frontend\models\Cart;
use yii\base\ErrorException;
use yii\web\Cookie;

class CookieHandler extends \yii\base\Component
{
    private $_cart=[];

    public function __construct(array $config=[])
    {
        $cookies = \Yii::$app->request->cookies;
        $cookie = $cookies->get('cart');
        if($cookie == null){//购物车cookie不存在
            $cart = [];
        }else{//购物车cookie存在
            $cart = unserialize($cookie->value);
        }
        $this->_cart = $cart;
        parent::__construct($config);
    }
    //获取cookie购物车数据
    public function getCart()
    {
        return $this->_cart;
    }


    //修改cookie购物车数据
    public function updateCart($goods_id,$num=1){
        $this->_cart[$goods_id] = $num;
        return $this;
    }
    //添加cookie购物车数据
    public function addCart($goods_id,$num=1)
    {
        if(array_key_exists($goods_id,$this->_cart)){
            $this->_cart[$goods_id] += $num;
        }else{
            $this->_cart[$goods_id] = $num;
        }
        return $this;
    }

    //删除cookie购物车数据
    public function delCart($goods_id){
        unset($this->_cart[$goods_id]);
        return $this;//返回当前对象，实现连贯操作
    }

    public function save(){
        //将购车数据保存回cookie
        $cookies = \Yii::$app->response->cookies;
        $cookie = new Cookie([
            'name'=>'cart',
            'value'=>serialize($this->_cart)
        ]);

        $cookies->add($cookie);
    }
    //清空cookie购物车
    public function flushCart()
    {
        $this->_cart = [];
        return $this;
    }
    //同步到购物车数据库
    public function synDb()
    {
        if(\Yii::$app->user->isGuest){
            throw new ErrorException('必须登录后才能保存到数据库');
        }
        $member_id = \Yii::$app->user->id;
        foreach($this->_cart as $goods_id=>$num){
            $cart = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);
            if($cart == null){
                $cart = new Cart();
                $cart->member_id = $member_id;
                $cart->goods_id = $goods_id;
            }
            $cart->amount = $num;
            $cart->save();
        }
        return $this;
    }

}
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
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderDetail;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\HttpException;
use EasyWeChat\Foundation\Application;
use Endroid\QrCode\QrCode;


class IndexController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'access'=>[
                'class'=>AccessControl::className(),
                'only'=>['order'],
                'rules'=>[
                  [
                      'allow'=>true,
                      'actions'=>['order'],
                      'roles'=>['@'],
                  ]
                ],
            ],
        ];
    }


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
            $cart = ArrayHelper::map(Cart::find()->where(['member_id'=>\Yii::$app->user->id])->asArray()->all(),'id','amount');
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
        //var_dump($models);exit;

        return $this->render('cart',['models'=>$models]);
    }

    /*
     * 订单确认页
     */
    public function actionOrder()
    {
        $this->layout = 'cart';

        $order = new Order();
        if($order->load(\Yii::$app->request->post())){
            $order->member_id = \Yii::$app->user->id;
            $address = Address::findOne(['id'=>\Yii::$app->request->post('address_id'),'member_id'=>$order->member_id]);
            if($address==null){
                throw new HttpException('404','地址不存在');
            }
            $order->name = $address->name;
            $order->province = $address->province;
            $order->city = $address->city;
            $order->area = $address->area;
            $order->detail_address = $address->detail;
            $order->tel = $address->tel;
            if($order->validate()){
                $order->delivery_name = Order::$deliveries[$order->delivery_id][0];
                $order->delivery_price = Order::$deliveries[$order->delivery_id][1];
                $order->payment_name = Order::$payments[$order->payment_id][0];



                $order->price = 0;//计算总价
                //如果支付方式是货到付款，则状态是 待发货；如果是在线支付，则状态是 待付款
                $order->status = 1;
                $order->create_time = time();

                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();//开启事务
                try {
                    //$order->save();

                    //订单详情表数据
                    //从购物车数据表获取商品数据 $carts = [['goods_id'=>1,'amount'=>2],[]]
                    //遍历购物车数据
                    //foreach($carts as $cart){
                    //$order_detail = new OrderDetail();
                    //order_info_id  $order->save()--->$order->id;
                    //goods_name logo price 根据goods_id获取商品信息，从商品信息中获取
                    //$goods = Goods::findOne(['id'=>$goods_id]);
                    //!TODO 检查库存  amount <= $goods->stock
                    //if(amount > $goods->stock){
                    //库存不足

                    //抛出异常
                    //throw new Exception('商品xxx的库存不足');

                    //}
                    //total_price = price * amount
                    //$order_detail->save();
                    //}
                    $transaction->commit();//提交事务
                }catch(Exception $e){
                    $transaction->rollBack();//回滚
                    //设置提示信息，
                    //\Yii::$app->session->setFlash('danger','商品xxx的库存小于xxx，请修改数量后重新下单');
                }
                //事务 解决库存不足，需要回滚
                //前提：数据表存储引擎必须是 innodb

            }

        }


        $cart = Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();
        $models=[];
        foreach($cart as $item){
            $goods = Goods::find()->where(['id'=>$item->id])->asArray()->one();
            $goods['num']=$item->amount;
            $models[]=$goods;
        }
        return $this->render('order',['models'=>$models]);
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

    public function actionWechatOrder()
    {
        $order = Order::findOne(['id'=>1]);

        return $this->render('wechat-order',['order'=>$order]);
    }

    public function actionWechatPay($id)
    {
        $model = Order::findOne(['id'=>$id]);


        $options = [
            /**
             * Debug 模式，bool 值：true/false
             *
             * 当值为 false 时，所有的日志都不会记录
             */
            'debug'  => false,
            /**
             * 账号基本信息，请从微信公众平台/开放平台获取
             */
            'app_id'  => 'wx85adc8c943b8a477',         // AppID
            'secret'  => 'a687728a72a825812d34f307b630097b',     // AppSecret
            'token'   => 'your-token',          // Token
            'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！
            /**
             * 日志配置
             *
             * level: 日志级别, 可选为：
             *         debug/info/notice/warning/error/critical/alert/emergency
             * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
             * file：日志文件位置(绝对路径!!!)，要求可写权限
             */
            /*'log' => [
                'level'      => 'debug',
                'permission' => 0777,
                'file'       => '/tmp/easywechat.log',
            ],*/
            /**
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址
             */
            /*'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/examples/oauth_callback.php',
            ],*/
            /**
             * 微信支付
             */
            'payment' => [
                'merchant_id'        => '1228531002',//商户号
                'key'                => 'a687728a72a825812d34f307b630097b',//登录商户号设置
                //SSL认证的证书路径
                //'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
                //'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
                // 'device_info'     => '013467007045764',
                // 'sub_app_id'      => '',
                // 'sub_merchant_id' => '',
                // ...
            ],
            /**
             * Guzzle 全局设置
             *
             * 更多请参考： http://docs.guzzlephp.org/en/latest/request-options.html
             */
            'guzzle' => [
                'timeout' => 5.0, // 超时时间（秒）
                'verify' => false, // 关掉 SSL 认证（强烈不建议！！！）
            ],
        ];
        $app = new Application($options);
        $payment = $app->payment;
        //调用统一下单api



        $attributes = [
            //JSAPI--公众号支付、NATIVE--原生扫码支付、APP--app支付，统一下单接口trade_type的传参可参考这里
            //MICROPAY--刷卡支付，刷卡支付有单独的支付接口，不调用统一下单接口
            'trade_type'       => 'NATIVE', // JSAPI，NATIVE，APP...
            'body'             => '京西商城订单',//商品描述
            'detail'           => 'iPad mini4 32G 白色等',//商品详情
            'out_trade_no'     => $model->trade_no,//订单编号
            'total_fee'        => $model->price*100, // 单位：分
            'notify_url'       => Url::to(['index/order-notify'],true),
            //必须是微信服务器能够访问的地址（代码必须放到服务器，不能是本地）
            // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            //'openid'           => '当前用户的 openid', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];
        $order = new \EasyWeChat\Payment\Order($attributes);

        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            //$prepayId = $result->prepay_id;
            //var_dump($result);//请求结果
            $code_url = $result->code_url;//预支付交易链接code_url
            //把code_url生成二维码

            //$code_url = 'weixin://wxpay/bizpayurl?pr=YMCImbh';
            // Create a QR code
            $qrCode = new QrCode($code_url);
            $qrCode->setSize(300);

            // Advanced options
            /*$qrCode
                ->setQuietZone(2)
                ->setErrorCorrectionLevel('H')
                ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
                ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
                ->setLabel('Scan the code', 16, __DIR__.'/../font/open_sans.ttf')
                ->setLogo(__DIR__.'/../logo/endroid.png', 50)
            ;*/

            // now we can output the QR code
            header('Content-Type: '.$qrCode->getContentType(QrCode::IMAGE_TYPE_PNG));
            $qrCode->render();
            exit;
        }
        //var_dump($result);//请求结果
    }

    //支付结果通知网址
    public function actionOrderNotify(){

        $options = [
            /**
             * Debug 模式，bool 值：true/false
             *
             * 当值为 false 时，所有的日志都不会记录
             */
            'debug'  => false,
            /**
             * 账号基本信息，请从微信公众平台/开放平台获取
             */
            'app_id'  => 'wx85adc8c943b8a477',         // AppID
            'secret'  => 'a687728a72a825812d34f307b630097b',     // AppSecret
            'token'   => 'your-token',          // Token
            'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！
            /**
             * 日志配置
             *
             * level: 日志级别, 可选为：
             *         debug/info/notice/warning/error/critical/alert/emergency
             * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
             * file：日志文件位置(绝对路径!!!)，要求可写权限
             */
            /*'log' => [
                'level'      => 'debug',
                'permission' => 0777,
                'file'       => '/tmp/easywechat.log',
            ],*/
            /**
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址
             */
            /*'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/examples/oauth_callback.php',
            ],*/
            /**
             * 微信支付
             */
            'payment' => [
                'merchant_id'        => '1228531002',//商户号
                'key'                => 'a687728a72a825812d34f307b630097b',//登录商户号设置
                //SSL认证的证书路径
                //'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
                //'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
                // 'device_info'     => '013467007045764',
                // 'sub_app_id'      => '',
                // 'sub_merchant_id' => '',
                // ...
        ],
        /**
         * Guzzle 全局设置
         *
         * 更多请参考： http://docs.guzzlephp.org/en/latest/request-options.html
         */
        'guzzle' => [
            'timeout' => 5.0, // 超时时间（秒）
            'verify' => false, // 关掉 SSL 认证（强烈不建议！！！）
        ],
        ];
        $app = new Application($options);
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::findOne(['trade_no'=>$notify->out_trade_no]);
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->status!=1) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                //$order->paid_at = time(); // 更新支付时间为当前时间
                $order->status = 2;
            } else { // 用户支付失败
                //$order->status = 'paid_fail';
            }
            $order->save(); // 保存订单
            return true; // 返回处理完成
        });
        return $response;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/17
 * Time: 10:14
 */

namespace console\controllers;


use backend\models\Goods;
use frontend\models\Order;
use frontend\models\OrderDetail;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class TaskController extends Controller
{
    //清理超时未支付订单   下单成功1小时内必须支付
    //yii 控制台命名   yii task/clear
    public function actionClear()
    {
        set_time_limit(0);//修改脚本的最大执行时间，0表示没有时间限制，一直执行
        while(true){//死循环，循环执行清理
            //1 找出超时订单id(条件：状态是1未支付，下单时间超过1小时)
            //(time()-下单时间戳) > 3600  ===>   下单时间戳 < time()-3600
            $orders = Order::find()->select('id')->where(['status'=>1])->andWhere(['<','create_time',time()-3600])->asArray()->all();
            $ids = ArrayHelper::map($orders,'id','id');
            echo implode(',',$ids).'order has completed'.date('Y-m-d H:i:s')."\r\n";

            //2 修改订单状态
            //Order::updateAll(['status'=>0],'status=1 AND create_time < '.(time()-3600));
            //3 返库存
            /*foreach($ids as $id){
                $details = OrderDetail::find()->where(['order_info_id'=>$id])->all();
                foreach($details as $detail){
                    Goods::updateAllCounters(['stock'=>$detail->amount],'id='.$detail->goods_id);
                }
            }*/
            //循环间隔时间，每秒执行一次
            sleep(1);
        }

    }
}
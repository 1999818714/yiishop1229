<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $detail_address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property string $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $price
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    //快递方式
    public static $deliveries=[
        1=>['顺丰快递','20','速度非常快，服务非常好，价格稍贵'],
        2=>['申通快递','10','速度快，服务好，价格便宜'],
        3=>['EMS','12','速度一般，服务好，价格便宜'],
    ];
    //支付方式
    public static $payments=[
        1=>['货到付款','送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        2=>['支付宝','在线支付'],
        3=>['微信支付','在线支付'],
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'delivery_id', 'payment_id', 'status', 'create_time'], 'integer'],
            [['delivery_price', 'price'], 'number'],
            [['name', 'province', 'city', 'area', 'delivery_name', 'payment_name', 'trade_no'], 'string', 'max' => 20],
            [['detail_address'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'name' => 'Name',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'detail_address' => 'Detail Address',
            'tel' => 'Tel',
            'delivery_id' => 'Delivery ID',
            'delivery_name' => 'Delivery Name',
            'delivery_price' => 'Delivery Price',
            'payment_id' => 'Payment ID',
            'payment_name' => 'Payment Name',
            'price' => 'Price',
            'status' => 'Status',
            'trade_no' => 'Trade No',
            'create_time' => 'Create Time',
        ];
    }
}

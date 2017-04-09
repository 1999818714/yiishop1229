<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $detail
 * @property string $tel
 * @property integer $is_default
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'is_default'], 'integer'],
            [['name', 'province', 'city', 'area', 'detail', 'tel'], 'required'],
            [['name', 'province', 'city', 'area'], 'string', 'max' => 50],
            [['detail'], 'string', 'max' => 255],
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
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'area' => '县',
            'detail' => '详细地址',
            'tel' => '手机号码',
            'is_default' => '默认地址',
        ];
    }
}

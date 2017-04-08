<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $url
 * @property string $description
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 20],
            [['url'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => '上级分类',
            'name' => '名称',
            'url' => '路由（权限）',
            'description' => '描述',
        ];
    }

    /*
     * 获取所有一级分类菜单选项
     */
    public static function getParentOptions()
    {
        $options =  ArrayHelper::map(self::find()->where(['parent_id'=>0])->asArray()->all(),'id','name');
        return ArrayHelper::merge(['0'=>'顶级分类'],$options);
    }

    /*
     * 建立一级菜单和二级菜单的关系 1对多
     */
    public function getChildren(){
        return $this->hasMany(self::className(),['parent_id'=>'id']);
    }
}

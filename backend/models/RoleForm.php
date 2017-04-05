<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/5
 * Time: 9:35
 */

namespace backend\models;


use yii\base\Model;
use yii\helpers\ArrayHelper;

class RoleForm extends Model
{
    public $name;//角色名
    public $description;//描述
    public $permissions=[];//权限

    const SCENARIO_ADD = 'add';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios,[
            self::SCENARIO_ADD => ['name','description','permissions'],
            //self::SCENARIO_DEFAULT => ['name','description','permissions'],
        ]);
    }

    public function rules()
    {
        return [
            [['name','description'],'required'],
            //角色名不能重复
            ['name','validateName','on'=>self::SCENARIO_ADD],
            ['permissions','safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>  '名称',
            'description'=>  '描述',
            'permissions'=>  '权限',
        ];
    }

    /*
     * 自定义验证方法
     * 只处理错误情况
     */
    public function validateName($attribute, $params)
    {
        //判断权限是否存在
        $authManager = \Yii::$app->authManager;
        if($authManager->getRole($this->$attribute)){
            $this->addError($attribute,'角色已存在');
        }
    }

    //获取所有权限选项
    public static function getPermissionOptions()
    {
        $permissions = \Yii::$app->authManager->getPermissions();

        return ArrayHelper::map($permissions,'name','description');
    }

}
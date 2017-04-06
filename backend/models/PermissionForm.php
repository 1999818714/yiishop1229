<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/5
 * Time: 9:35
 */

namespace backend\models;


use yii\base\Model;

class PermissionForm extends Model
{
    public $name;//权限名（路由） user/add
    public $description;//描述

    public function rules()
    {
        return [
            [['name','description'],'required'],
            //权限名不能重复
            ['name','validateName'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>  '名称（路由）',
            'description'=>  '描述',
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
        if($authManager->getPermission($this->$attribute)){
            $this->addError($attribute,'权限已存在');
        }
    }

    /*
     * add() 保存权限
     */
    public function add()
    {
        $authManager = \Yii::$app->authManager;
        //1 添加user/add权限
        $permission = $authManager->createPermission($this->name);//创建权限
        $permission->description = $this->description;
        //添加到数据表
        return $authManager->add($permission);
    }
}
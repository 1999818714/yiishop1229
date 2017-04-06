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
use yii\rbac\Role;

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

    /*
     * 添加角色  修改角色
     */
    public function save($role=null)
    {
        $authManager = \Yii::$app->authManager;
        //同时处理添加和修改角色  根据场景判断是添加还是修改
        //添加角色权限
        if($this->scenario == self::SCENARIO_ADD){
            $role = $authManager->createRole($this->name);//创建角色
            $role->description = $this->description;
            //添加到数据表
            $authManager->add($role);
            //给角色关联权限
            //$this->permissions = ["goods/add" ,"goods/index"];
            /*foreach($this->permissions as $permission){
                $permissionModel = $authManager->getPermission($permission);
                $authManager->addChild($role,$permissionModel);//角色对象   权限对象
            }*/
        }else{
            //修改用户角色
            //保存角色旧名称
            $oldName = $role->name;
            //将表单数据赋值给角色
            $role->name = $this->name;
            $role->description = $this->description;
            //更新角色  update()方法第一个参数是角色的旧名称，需要先保存下来，避免被覆盖
            $authManager->update($oldName,$role);
            //关联权限
            //清除所有已关联的权限
            $authManager->removeChildren($role);


        }
        //关联权限
        foreach($this->permissions as $permission){
            $permissionModel = $authManager->getPermission($permission);
            $authManager->addChild($role,$permissionModel);//角色对象   权限对象
        }


    }
    /*
     * 从角色加载数据到表单模型
     */
    public function loadFromRole(Role $role)
    {
        $this->name = $role->name;
        $this->description = $role->description;

        //获取角色的所有权限
        $permissions = \Yii::$app->authManager->getPermissionsByRole($role->name);
        /*
         * $permisions = [["brand/index"]=>
  object(yii\rbac\Permission)#72 (7) {
    ["type"]=>
    string(1) "2"
    ["name"]=>
    string(11) "brand/index"
    ["description"]=>
    string(12) "品牌列表"
    ["ruleName"]=>
    NULL
    ["data"]=>
    NULL
    ["createdAt"]=>
    string(10) "1491357922"
    ["updatedAt"]=>
    string(10) "1491357922"
  ]
         */
        //$this->permissions = ["goods/add" ,"goods/index"];
        /*foreach($permissions as $key=>$permission){
//            $this->permissions[] = $key;
            $this->permissions[] = $permission->name;
        }*/
        $this->permissions = array_keys($permissions);
    }


}
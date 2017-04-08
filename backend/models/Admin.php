<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_time
 * @property string $last_login_ip
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{
    /*
     * 定义场景
     */
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_REGISTER = 'register';

    public function scenarios()
    {
        $scenarios = [
            self::SCENARIO_LOGIN => ['username', 'password_hash'],
            self::SCENARIO_REGISTER => ['username', 'password_hash','email'],
        ];
        $scenarios2 =  parent::scenarios();
        return ArrayHelper::merge($scenarios,$scenarios2);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password_hash', 'email'], 'required'],
            [['status', 'created_at', 'updated_at', 'last_login_time'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['last_login_ip'], 'string', 'max' => 15],
            [['username'], 'unique','on'=>[self::SCENARIO_REGISTER]],//on指定验证规则生效场景
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_login_time' => 'Last Login Time',
            'last_login_ip' => 'Last Login Ip',
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {

        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {

    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;

    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key == $authKey;
    }

    public function getMenuItems()
    {
        $menuItems = [];
        //根据当前用户的权限获取菜单
        //遍历所有菜单，判断当前用户是否有对应权限
        //>1 获取所有1级菜单

        $menus = Menu::find()->where(['parent_id'=>0])->all();
        foreach($menus as $menu){
            //根据菜单之间的关系（一级菜单--》二级菜单  1对多）
            $items = [];
            foreach($menu->children as $child){
                //判断用户是否有该权限
                if(Yii::$app->user->can($child->url)){
                    $items[] = ['label' => $child->name, 'url' => [$child->url]];
                }
            }
            //至少有一个二级菜单权限才显示该菜单组
            if(!empty($items)){
                $menuItems[] = [
                    'label' => $menu->name,
                    'items' => $items,
                    /*[
                        ['label' => '商品列表', 'url' => ['goods/index']],
                        ['label' => '添加商品', 'url' => ['goods/add']],
                    ]*/
                ];
            }

        }
        return $menuItems;

        /*return [
            'label' => '商品管理',
            'items' => [
                ['label' => '商品列表', 'url' => ['goods/index']],
                ['label' => '添加商品', 'url' => ['goods/add']],
            ],
        ];*/

    }
}

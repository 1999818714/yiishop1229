<?php

namespace frontend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "member".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $tel
 * @property integer $last_login_time
 * @property integer $last_login_ip
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $password;//明文密码
    public $repassword;//确认密码
    public $code;//图形验证码
    public $smscode;//短信验证码
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password','repassword', 'email', 'tel','code','smscode'], 'required'],
            [['last_login_time', 'last_login_ip', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['tel'], 'string', 'max' => 11],
            [['username','email','tel'], 'unique'],

            //验证码
            ['code','captcha'],
            //验证邮箱
            ['email','email'],
            //验证手机号码
            ['tel','match','pattern'=>'/^1\d{10}$/','message'=>'手机号码格式不正确'],
            //两次密码一致
            ['repassword','compare','compareAttribute'=>'password','message'=>'两次密码不一致'],
            //验证短信验证码
            ['smscode','validateSmscode']
        ];
    }

    public function validateSmscode()
    {
        //根据电话号码从session获取短信验证码
        $code = Yii::$app->session->get('tel_'.$this->tel);
        //和表单提交的短信验证码对比
        if($code != $this->smscode){
            $this->addError('smscode','验证码不正确');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名：',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password' => '密码：',
            'repassword' => '确认密码：',
            'email' => '邮箱：',
            'tel' => '电话：',
            'smscode' => '短信验证码：',
            'code' => '验证码：',
            'last_login_time' => 'Last Login Time',
            'last_login_ip' => 'Last Login Ip',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if($this->password){
            //给明文密码加密
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        }
        if($insert){
            $this->created_at  = time();
            $this->status = 1;
            $this->auth_key = Yii::$app->security->generateRandomString();
        }else{
            $this->updated_at = time();
        }

        return parent::beforeSave($insert);
    }

    /*
     * 用户和地址的关系  1对多
     */
    public function getAddresses(){
        return $this->hasMany(Address::className(),['member_id'=>'id']);
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
        return self::findOne($id);
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
}

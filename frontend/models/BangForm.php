<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/23
 * Time: 10:13
 */

namespace frontend\models;


use yii\base\Model;

class BangForm extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['username','password'],'required'],
        ];
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/9
 * Time: 10:08
 */

namespace frontend\assets;


use yii\web\AssetBundle;
use yii\web\JqueryAsset;


class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'style/login.css',
    ];
    public $js = [
    ];
    public $depends = [
        'frontend\assets\IndexAsset',
    ];
}
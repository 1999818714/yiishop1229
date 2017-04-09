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
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/login.css',
        'style/footer.css',
    ];
    public $js = [
    ];
    public $depends = [
        //JqueryAsset::className(),
        'yii\web\JqueryAsset',
        //'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
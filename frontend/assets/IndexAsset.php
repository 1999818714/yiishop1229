<?php
namespace frontend\assets;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/bottomnav.css',
        'style/footer.css',
    ];
    public $js = [
        "js/header.js",
	    "js/home.js"
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
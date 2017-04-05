<?php
return [
    'language'=>'zh-CN',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //配置RBAC
        'authManager'=>[
            'class'=>\yii\rbac\DbManager::className(),
        ]
    ],
];

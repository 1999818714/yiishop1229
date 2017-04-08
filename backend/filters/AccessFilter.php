<?php

namespace backend\filters;
use yii\base\ActionFilter;
use yii\web\HttpException;

class AccessFilter extends ActionFilter
{
    //操作执行之前要执行的代码
    public function beforeAction($action)
    {
        //判断当前用户是否拥有当前操作的权限
        if (!\Yii::$app->user->can($action->uniqueId)) {

            //当前操作(路由 goods/add)
            //$action->uniqueId;

            //判断当前用户是否已登录，如果未登录，则跳转到登录页面
            if(\Yii::$app->user->isGuest){
                //$action->controller 通过操作获取所属控制器对象
                return $action->controller->redirect(\Yii::$app->user->loginUrl);
            }

            //抛出一个403没有权限的状态码（异常）
            throw new HttpException(403,'对不起，您没有该操作权限');

        //禁止操作继续执行
            return false;
        }
        return parent::beforeAction($action);
    }
}
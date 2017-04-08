<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 10:25
 */

namespace backend\controllers;


use backend\models\Menu;
use yii\web\Controller;

class MenuController extends Controller
{
    /*
     * 添加菜单
     */
    public function actionAdd()
    {
        $model = new Menu();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','菜单添加成功');
            return $this->redirect(['menu/index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    /*
     * 修改菜单
     */
    public function actionEdit($id)
    {
        $model = Menu::findOne($id);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','菜单更新成功');
            return $this->redirect(['menu/index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    /*
     * 菜单列表
     */
    public function actionIndex()
    {
        $models = Menu::findAll(['parent_id'=>0]);

        return $this->render('index',['models'=>$models]);
    }
    /*
     * 删除菜单
     */

}
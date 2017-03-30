<?php

namespace backend\controllers;

use backend\models\GoodsCategory;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class GoodsCategoryController extends \yii\web\Controller
{
    //public $layout = false;
    public function actionIndex()
    {
        $models = GoodsCategory::find()->orderBy(['tree'=>SORT_ASC,'lft'=>SORT_ASC])->all();
        return $this->render('index',['models'=>$models]);
    }

    /**
     * 商品分类添加
     */
    public function actionAdd()
    {
        $model = new GoodsCategory();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->parent_id == 0){
                $model->makeRoot();//创建一级分类

            }else{
                //创建非一级分类
                //1 查找父分类
                $parent_cate = GoodsCategory::findOne(['id'=>$model->parent_id]);
                $model->prependTo($parent_cate);

            }
            \Yii::$app->session->setFlash('success','分类添加成功');
            return $this->refresh();//刷新本页(跳转到当前页)
        }
        $models = GoodsCategory::find()->asArray()->all();
        $models[] = ['id'=>0,'parent_id'=>0,'name'=>'顶级分类'];
        $models = Json::encode($models);
        return $this->render('add',['model'=>$model,'models'=>$models]);
    }

    public function actionTest2()
    {
        $cate = new GoodsCategory(['name' => '手机/运营商/数码']);
        //$cate->tree = 0;
        $cate->parent_id = 0;
        $cate->makeRoot();
        //var_dump($cate->getErrors());

        $cate1_1 = new GoodsCategory(['name' => '手机通讯']);
        $cate1_1->parent_id = 1;
        $cate1_2 = new GoodsCategory(['name' => '手机配件']);
        $cate1_2->parent_id = 1;
        $cate1_1->prependTo($cate);
        $cate1_2->prependTo($cate);
    }



    /*
     * 测试ztree插件
     */
    public function actionTest()
    {
        $models = GoodsCategory::find()->all();
        //$this->layout = false; =====   renderPartial
        return $this->renderPartial('test',['models'=>$models]);
    }

}

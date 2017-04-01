<?php

namespace backend\controllers;

use backend\models\Goods;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use xj\uploadify\UploadAction;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class GoodsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $query = Goods::find();
        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'pageSize'=>5
        ]);

        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    /*
     * 添加商品
     */
    public function actionAdd()
    {
        $model = new Goods();
        $introModel = new GoodsIntro();
        if($model->load(\Yii::$app->request->post()) && $introModel->load(\Yii::$app->request->post())){
            $model->logo_file = UploadedFile::getInstance($model,'logo_file');
            if($model->validate() && $introModel->validate()){
                if($model->logo_file){
                    $fileName = 'upload/logo/'.uniqid().'.'.$model->logo_file->extension;
                    $model->logo_file->saveAs($fileName,false);
                    $model->logo = $fileName;
                }
                /*
                 * 处理sn
                 * 自动生成sn,规则为年月日+今天的第几个商品,比如201704010001
                 *
                 */
                $day = date('Y-m-d');
                $goodsCount = GoodsDayCount::findOne(['day'=>$day]);
                if($goodsCount==null){
                    $goodsCount = new GoodsDayCount();
                    $goodsCount->day = $day;
                    $goodsCount->count = 0;
                    $goodsCount->save();
                }

                $model->sn = date('Ymd').preg_replace('/(\d{4})$/','$1','000'.($goodsCount->count+1));
                $model->save();
                $introModel->goods_id = $model->id;
                $introModel->save();
                GoodsDayCount::updateAllCounters(['count'=>1],['day'=>$day]);

                \Yii::$app->session->setFlash('success','商品添加成功,请添加商品相册');
                return $this->redirect(['goods/gallery','id'=>$model->id]);
            }
        }
        return $this->render('add',['model'=>$model,'introModel'=>$introModel]);
    }
    /*
     * 修改商品信息
     */
    public function actionEdit($id){
        $model = Goods::findOne(['id'=>$id]);
        $introModel = $model->goodsIntro;

        return $this->render('add',['model'=>$model,'introModel'=>$introModel]);
    }
    /*
     * 商品相册
     */
    public function actionGallery($id)
    {
        $goods = Goods::findOne(['id'=>$id]);
        if($goods == null){
            throw new NotFoundHttpException('商品不存在');
        }


        return $this->render('gallery',['goods'=>$goods]);

    }

    /*
     * AJAX删除图片
     */
    public function actionDelGallery(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsGallery::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'success';
        }else{
            return 'fail';
        }

    }


    public function actions() {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "",//图片访问路径前缀
                    "imagePathFormat" => "/upload/{yyyy}{mm}{dd}/{time}{rand:6}" ,//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot"),
            ],
            ],

            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload/logo',
                'baseUrl' => '@web/upload/logo',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /*'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "/{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $model = new GoodsGallery();
                    $model->goods_id = \Yii::$app->request->post('goods_id');
                    $model->path = $action->getWebUrl();
                    $model->save();
                    $action->output['fileUrl'] = $model->path;
                    //$action->output['goods_id'] = $model->goods_id;

//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //$action->output['Path'] = $action->getSavePath();
                    /*
                     * 将图片上传到七牛云
                     */
                   /* $qiniu = \Yii::$app->qiniu;//实例化七牛云组件
                    $qiniu->uploadFile($action->getSavePath(),$action->getFilename());//将本地图片上传到七牛云
                    $url = $qiniu->getLink($action->getFilename());//获取图片在七牛云上的url地址
                    $action->output['fileUrl'] = $url;//将七牛云图片地址返回给前端js
                   */
                },
            ],
        ];
    }

}

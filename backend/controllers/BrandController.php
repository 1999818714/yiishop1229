<?php

namespace backend\controllers;

use backend\filters\AccessFilter;
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\UploadedFile;
use xj\uploadify\UploadAction;
use crazyfd\qiniu\Qiniu;

class BrandController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'accessFilter'=>[
                'class'=>AccessFilter::className(),
                //'only'=>['index','add','edit','s-upload'],//指定需要进行权限控制的操作
            ],
        ];
    }
    public function actionLogin()
    {
        return 'login';
    }

    /*
     * 品牌列表
     */
    public function actionIndex()
    {
        $query = Brand::find()->where(['!=','status','-1']);

        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'pageSize'=>3,
        ]);

        $models = $query->limit($pager->limit)->offset($pager->offset)->all();

        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }

    /*
     * 添加品牌
     */
    public function actionAdd()
    {
        $model = new Brand();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());

            //$model->logo_file = UploadedFile::getInstance($model,'logo_file');
            if($model->validate()){
                /*if($model->logo_file){
                    $fileName = 'upload/brand/'.uniqid().'.'.$model->logo_file->extension;
                    if($model->logo_file->saveAs($fileName,false)){
                        $model->logo = $fileName;
                    }
                }*/
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['brand/index']);
            }
        }

        //$model->status = 1;
        return $this->render('add',['model'=>$model]);

       // Yii::createObject();
    }



    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload/brand',
                'baseUrl' => '@web/upload/brand',
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
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //$action->output['fileUrl'] = $action->getWebUrl();
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //$action->output['Path'] = $action->getSavePath();
                    /*
                     * 将图片上传到七牛云
                     */
                    $qiniu = \Yii::$app->qiniu;//实例化七牛云组件
                    $qiniu->uploadFile($action->getSavePath(),$action->getFilename());//将本地图片上传到七牛云
                    $url = $qiniu->getLink($action->getFilename());//获取图片在七牛云上的url地址
                    $action->output['fileUrl'] = $url;//将七牛云图片地址返回给前端js
                },
            ],
        ];
    }


    public function actionTest()
    {
//        $fileName = \Yii::getAlias('@webroot');
//        var_dump($fileName);exit;
        /*$ak = 'hqNnJqiC0r7xoCcroZKMbqgbmELaZPyYmrbnNIDg';
        $sk = 'KwzsOiQ7UbAesjwXKh5fMblJCbbrOHuN6grCQxzq';
        $domain = 'http://onk0ygatz.bkt.clouddn.com/';
        $bucket = 'yiishop-php1229';*/

        //$qiniu = new Qiniu($ak, $sk,$domain, $bucket);
        /*$qiniu = \Yii::$app->qiniu;
        $key = '58d9db9421bc7.png';//指定上传到七牛云的文件名
        $fileName = \Yii::getAlias('@webroot').'/upload/brand/58d9db9421bc7.png';
        $qiniu->uploadFile($fileName,$key);
        $url = $qiniu->getLink($key);
        return $url;*/
    }
}

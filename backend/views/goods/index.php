<?php
/* @var $this yii\web\View */
?>
<h1>goods/index</h1>
<?php
$form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'get',
    //get方式提交,需要显式指定action
    'action'=>\yii\helpers\Url::to(['goods/index']),
    'options'=>['class'=>'form-inline']
]);
echo $form->field($model,'name')->textInput(['placeholder'=>'商品名'])->label(false);
echo $form->field($model,'sn')->textInput(['placeholder'=>'货号'])->label(false);
echo $form->field($model,'minPrice')->textInput(['placeholder'=>'￥'])->label(false);
echo $form->field($model,'maxPrice')->textInput(['placeholder'=>'￥'])->label('-');
echo \yii\bootstrap\Html::submitButton('搜索');
\yii\bootstrap\ActiveForm::end();
?>
<table class="table">
    <tr>
        <th>ID</th>
        <th>货号</th>
        <th>名称</th>
        <th>价格</th>
        <th>库存</th>
        <th>LOGO</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->sn?></td>
        <td><?=$model->name?></td>
        <td><?=$model->shop_price?></td>
        <td><?=$model->stock?></td>
        <td><?=\yii\bootstrap\Html::img('@web/'.$model->logo,['style'=>'max-height:50px'])?></td>
        <td>
            <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-picture"></span>相册',['gallery','id'=>$model->id],['class'=>'btn btn-default'])?>
            <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-edit"></span>编辑',['edit','id'=>$model->id],['class'=>'btn btn-default'])?>
            <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-trash"></span>删除',['del','id'=>$model->id],['class'=>'btn btn-default'])?>
          </td>
    </tr>
    <?php endforeach;?>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager
])?>
<?php
/* @var $this yii\web\View */
?>
<h1>品牌列表</h1>
<table class="table table-bordered table-hover">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>图片</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php  foreach($models as $model):?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->name?></td>
        <td><?=\yii\bootstrap\Html::img('@web'.$model->logo,['style'=>'max-height:30px'])?></td>
        <td><?=\backend\models\Brand::$status_options[$model->status]?></td>
        <td>编辑 删除</td>
    </tr>
    <?php endforeach;?>
</table>

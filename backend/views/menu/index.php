<?php
/* @var $this yii\web\View */
?>
<table class="table table-bordered table-hover table-striped">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>路由</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->name?></td>
        <td><?=$model->url?></td>
        <td><?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$model->id],['class'=>'btn btn-success btn-xs'])?> 删除</td>
    </tr>
        <?php foreach($model->children as $child):?>
            <tr>
                <td><?=$child->id?></td>
                <td><?='&nbsp;&nbsp;&nbsp;&nbsp;'.$child->name?></td>
                <td><?=$child->url?></td>
                <td><?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$child->id],['class'=>'btn btn-success btn-xs'])?> 删除</td>
            </tr>
        <?php endforeach;?>
    <?php endforeach;?>
</table>

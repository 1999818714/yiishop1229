<table class="table">
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach($roles as $role):?>
    <tr>
        <td><?=$role->name?></td>
        <td><?=$role->description?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['rbac/edit-role','name'=>$role->name],['class'=>'btn btn-success'])?>
             删除</td>
    </tr>
    <?php endforeach;?>
</table>

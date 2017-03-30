<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/30
 * Time: 18:02
 * @var $this \yii\web\View
 */
?>
<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>操作</th>
    </tr>
    <tbody id="category">
    <?php foreach($models as $model):?>
    <tr data-lft="<?=$model->lft?>" data-rgt="<?=$model->rgt?>" data-tree="<?=$model->tree?>">
        <td><?=$model->id?></td>
        <td><?=str_repeat('－',$model->depth).$model->name?>
            <span class="glyphicon glyphicon-chevron-down expand" style="float: right"></span></td>
        <td>修改 删除</td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>
<?php
$js = <<<EOT
    $(".expand").click(function(){
        //切换图标样式
        $(this).toggleClass("glyphicon-chevron-down");
        $(this).toggleClass("glyphicon-chevron-up");
        //找出当前分类同一棵树下的子孙分类   同一颗树左值大于当前分类左值并且右值小于当前分类右值
        var current_tr = $(this).closest("tr");//获取当前点击图标所在tr
        var current_lft = current_tr.attr("data-lft");//当前分类左值
        var current_rgt = current_tr.attr("data-rgt");//当前分类右值
        var current_tree = current_tr.attr("data-tree");//当前分类tree值
        $("#category tr").each(function(){

            var lft = $(this).attr("data-lft");//分类的左值
            var rgt = $(this).attr("data-rgt");//分类的右值
            var tree = $(this).attr("data-tree");//分类的tree值
            if(tree == current_tree && lft > current_lft && rgt < current_rgt){
                //当前分类的子孙分类隐藏或显示
                $(this).fadeToggle();
            }
        });

    });
EOT;

$this->registerJs($js);
<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/style/address.css');
?>
<!-- 右侧内容区域 start -->
<div class="content fl ml10">
    <div class="address_hd">
        <h3>收货地址薄</h3>
        <?php foreach(Yii::$app->user->identity->addresses as $address):?>
            <dl>
                <dt><?=$address->name?>
                    <?=$address->province?>
                    <?=$address->city?>
                    <?=$address->area?>
                    <?=$address->detail?>
                    <?=$address->tel?> </dt>
                <dd>
                    <a href="">修改</a>
                    <a href="">删除</a>
                    <a href="">设为默认地址</a>
                </dd>
            </dl>
        <?php endforeach;?>
        <!--<dl class="last">
            <dt>2.许坤 四川省 成都市 高新区 仙人跳大街 17002810530 </dt>
            <dd>
                <a href="">修改</a>
                <a href="">删除</a>
                <a href="">设为默认地址</a>
            </dd>
        </dl>-->

    </div>

    <div class="address_bd mt10">
        <h4>新增收货地址</h4>
        <?php
        $form = \yii\widgets\ActiveForm::begin([
                'fieldConfig'=>[
                    'options'=>[
                        'tag'=>'li',
                    ]
                ]
        ]);
        echo '<ul>';
        echo $form->field($model,'name')->textInput(['class'=>'txt']);
        echo '<li style="display: inline-flex;"><label for="">所在地区：</label>';
        echo $form->field($model,'province',['options'=>['tag'=>false,'template' => "{input}"]])->dropDownList([''=>'请选择'])->label(false);
        echo $form->field($model,'city',['options'=>['tag'=>false,'template' => "{input}"]])->dropDownList([''=>'请选择'])->label(false);
        echo $form->field($model,'area',['options'=>['tag'=>false,'template' => "{input}"]])->dropDownList([''=>'请选择'])->label(false);
        echo '</li>';
        echo '</ul>';
        \yii\widgets\ActiveForm::end();
        ?>

    </div>

</div>
<!-- 右侧内容区域 end -->
<!--
>1 将省的选项填充
>2 选择一个省，显示该省对应的城市选项
>3 选择一个城市，显示该城市对应的县选项
-->
<?php
//加载地址数据
$this->registerJsFile('@web/js/address.js');
$this->registerJs(new \yii\web\JsExpression(
    <<<EOT
    //>1 将省的选项填充
    var options = '<option value="">请选择</option>';
    //循环遍历address，获取到每个元素（省）
    $(address).each(function(i,province){
        options += '<option>'+province.name+'</option>';
    });
    $("#address-province").html(options);

    //>2 选择一个省，显示该省对应的城市选项
    $("#address-province").change(function(){
        console.log($(this).val());
        //获取当前选中的省 $(this).val()
        var province_name = $(this).val();
        var options = '<option value="">请选择</option>';
        //循环遍历address，获取到每个元素（省）
        $(address).each(function(i,province){
            if(province_name == province.name){
                //var cities = province.city;//province.city 当前选中省的城市数据
                $(province.city).each(function(j,city){
                    options += '<option>'+city.name+'</option>';
                });
                return false;
            }
            console.log(i);
        });
        $("#address-city").html(options);
        //处理bug 清空县选项
        $("#address-area").html('<option value="">请选择</option>');
    });
    //>3 选择一个城市，显示该城市对应的县选项
    $("#address-city").change(function(){
       //获取当前选中的城市 $(this).val()
        var city_name = $(this).val();
        var province_name = $("#address-province").val();
        var options = '<option value="">请选择</option>';
        //循环遍历address，获取到每个元素（省）
        $(address).each(function(i,province){
            if(province_name == province.name){
                //var cities = province.city;//province.city 当前选中省的城市数据
                $(province.city).each(function(j,city){
                    //获取当前城市对应县的数据
                    if(city_name == city.name){
                        $(city.area).each(function(n,area){
                            options += '<option>'+area+'</option>';
                        });
                        return false;
                    }
                });
                return false;
            }
        });
        $("#address-area").html(options);
    });
EOT
));




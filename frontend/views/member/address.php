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
        echo $form->field($model,'province',['options'=>['tag'=>false,'template' => "{input}"]])->dropDownList([])->label(false);
        echo $form->field($model,'city',['options'=>['tag'=>false,'template' => "{input}"]])->dropDownList([])->label(false);
        echo $form->field($model,'area',['options'=>['tag'=>false,'template' => "{input}"]])->dropDownList([])->label(false);
        echo '</li>';
        echo '</ul>';
        \yii\widgets\ActiveForm::end();
        ?>

    </div>

</div>
<!-- 右侧内容区域 end -->


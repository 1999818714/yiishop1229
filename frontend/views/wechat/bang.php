<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password')->passwordInput();
echo \yii\bootstrap\Html::submitInput('绑定',['class'=>'btn btn-info btn-block']);
\yii\bootstrap\ActiveForm::end();
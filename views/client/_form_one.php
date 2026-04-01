<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">

    <?php $form = ActiveForm::begin([
        'id' => 'client-create-one-form',
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => "+\9\98##-###-##-##",
                'options' => [
                    'placeholder' => '+99800-000-00-00',
                    'class' => 'form-control',
                ],
            ]) ?>
        </div>
    </div>

    <?php if (!Yii::$app->request->isAjax): ?>
        <div class="form-group">
            <?= Html::submitButton(
                $model->isNewRecord ? 'Create' : 'Update',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) ?>
        </div>
    <?php endif; ?>

    <?php ActiveForm::end(); ?>

</div>
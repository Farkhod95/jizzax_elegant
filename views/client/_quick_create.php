<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">
    <?php $form = ActiveForm::begin([
        'id' => 'quick-client-form',
        'action' => ['client/quick-create'],
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'fio')->textInput([
                'maxlength' => true,
                'autocomplete' => 'off',
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => "+\9\98##-###-##-##",
                'options' => [
                    'placeholder' => '+99800-000-00-00',
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                ]
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'type')->hiddenInput(['value' => 1])->label(false) ?>
    <?= $form->field($model, 'total_debt')->hiddenInput(['value' => 0])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Saqlash', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
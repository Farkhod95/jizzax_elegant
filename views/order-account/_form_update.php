<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use unclead\multipleinput\MultipleInput;
/* @var $this yii\web\View */
/* @var $model app\models\OrderAccount */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'client_id')->label()->widget(\kartik\select2\Select2::classname(), [
                        'data' => $model->getClients(),
                        'options' => [
                            'placeholder' => Yii::t('app','Tanlang...'),
                            'disabled' => true
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'allowClear' => true,
                        ],
                            
                    ])->label('Mijoz'); ?> 
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'total_debt')->textInput(['type' => 'number', 'value' => 1233]) ?> 
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'exchange_rate')->textInput(['type' => 'number']) ?>
                </div>
            </div>
           

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'sum_dollar')->textInput(['type' => 'number'])->label("To'langan summa Dollarda") ?> 
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'sum_som')->textInput(['type' => 'number'])->label("To'langan summa So'mda") ?> 
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'sum_cart')->textInput(['type' => 'number'])->label("To'langan summa Kartada") ?> 
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'sum_transfers')->textInput(['type' => 'number'])->label("To'langan summa Transferda") ?>
                </div>
            </div>
            <?php if (!Yii::$app->request->isAjax){ ?>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Qo\'shish' : 'O\'zgartirish', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'style' => 'width:100%']) ?>
                </div>
            <?php } ?>

        <?php ActiveForm::end(); ?>
    </div>


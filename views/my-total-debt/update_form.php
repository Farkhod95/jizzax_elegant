<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\MyTotalDebt */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="my-total-debt-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'consignor_id')->label()->widget(\kartik\select2\Select2::classname(), [
                'data' => $model->getConsignor1(),
                'options' => [
                    'placeholder' => Yii::t('app','Tanlang...'),
                    'disabled' => true,
                ],
                'pluginOptions' => [
                    'tags' => true,
                    'allowClear' => true,
                ],
            ])->label('Yuk jo\'natuvchi'); ?> 
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'total_debts')->textInput(['value' =>0])->label("<b style='color:red'> To'lanadigan qarz miqdori ($)</b>") ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'chegirma')->textInput(['value' =>0]) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'cr_date')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => Yii::t('app','Sanani tanlang...'), 'required'=>True, 'value' => date('Y-m-d')],
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose'=>true, 
                    'format' => 'yyyy-mm-dd',
                ]
            ]);
            ?>
        </div>
    </div>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

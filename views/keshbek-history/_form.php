<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\KeshbekHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="keshbek-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row"> 
		<div class="col-md-6 col-xs-6">
			<?= $form->field($model, 'keshbek_sum')->input('number', [
    'step' => '0.01',     // 2 xonali kasr
    'min'  => '0',
    'placeholder' => 'Masalan: 12.50',
]) ?>
		</div>
		<div class="col-md-6 col-xs-6">
			<?= $form->field($model, 'cr_date')->widget(DatePicker::className(), [
                'options' => [
                    'placeholder' => 'Sanani tanlang...',
                    'required' => true,
                    'value' => date('d.m.Y'),
                ],
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy',
                    'todayBtn' => true,
                ],
            ]) ?>
		</div>
	</div>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\LossOfProfit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loss-of-profit-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row"> 
		<div class="col-md-6 col-xs-6">
			<?= $form->field($model, 'date_start')->widget(DatePicker::classname(), [
				'options' => ['placeholder' => Yii::t('app','Sanani tanlang...'), 'required'=>True, 'value' => date('d.m.Y')],
				'removeButton' => false,
				'pluginOptions' => [
					'autoclose'=>true, 
					'format' => 'dd.mm.yyyy',
				]
			]);
			?>
		</div>
		<div class="col-md-6 col-xs-6">
			<?= $form->field($model, 'date_end')->widget(DatePicker::classname(), [
					'options' => ['placeholder' => Yii::t('app','Sanani tanlang...'), 'required'=>True, 'value' => date('d.m.Y')],
					'removeButton' => false,
					'pluginOptions' => [
						'autoclose'=>true, 
						'format' => 'dd.mm.yyyy',
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

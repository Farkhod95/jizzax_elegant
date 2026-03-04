<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\Brands */
/** @var $freeSorting array|null */

$freeSorting = $freeSorting ?? \app\models\Brands::getFreeSorting(8);
$suggested   = $freeSorting[0] ?? 1;
?>

<div class="brands-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <datalist id="free-sorting-options">
        <?php foreach ($freeSorting as $n): ?>
            <option value="<?= (int)$n ?>"></option>
        <?php endforeach; ?>
    </datalist>

    <?= $form->field($model, 'sorting')
        ->input('number', [
            'min'  => 1,
            'list' => 'free-sorting-options',
            'placeholder' => $suggested,
        ])
        ->hint('Bo‘sh tartib raqamlari: <b>' . implode(', ', $freeSorting) . '</b>. Tavsiya: <a href="#" id="fill-sorting">'.$suggested.'</a>'); ?>

    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton(
                $model->isNewRecord ? 'Create' : 'Update',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
document.getElementById('fill-sorting')?.addEventListener('click', function(e){
  e.preventDefault();
  var val = this.textContent.trim();
  var inp = document.getElementById('brands-sorting');
  if (inp && val) inp.value = val;
});
JS;
$this->registerJs($js);

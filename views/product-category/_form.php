<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;
/* @var $this yii\web\View */
/* @var $model app\models\ProductCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-category-form">

    <?php $form = ActiveForm::begin(); ?>
		<div class="row">
			<div class="col-md-12">
                    <?= $form->field($model, 'brand_id')->label()->widget(\kartik\select2\Select2::classname(), [
                        'data' => $model->getBrands(),
                        'options' => [
                            'placeholder' => Yii::t('app','Tanlang...'),
                            'required' => true,
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'allowClear' => true,],
                    ]); ?> 
            </div>
			<div class="col-md-2">
				<?= $form->field($model, 'sorting')->textInput(['maxlength' => true]) ?>
			</div>	
			<div class="col-md-10">
				<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
			</div>
            	
		</div>
        <div class="row">
             <div class="col-md-12">
                    <?php echo $form->field($model, 'allValue')->widget(MultipleInput::className(), [
                        'id' => 'my_id',
                        'allowEmptyList' => true,
                        'enableGuessTitle' => true,
                        'columns' => [
                            [
                                'name'  => 'size',
                                'title' => 'O\'lchami <b style="color:red">(Misol: 9.99 )</b>',
                                'enableError' => true,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    // 'type' =>'number',
                                    'required' => true,
                                ],
                                'headerOptions' => [
                                    'style' => 'width: 180px;',
                                    
                                ] 
                            ],
                            [
                                'name' => 'type',
                                'title' => 'Tip',
                                'type' => \kartik\select2\Select2::className(),
                                'options' => [
                                    'data'  => $model->getType(),
                                    'options' => [
                                        'placeholder' => 'Tanlang...',
                                        'required' => true
                                    ],   
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],     
                                    'class' => 'input-priority',
                                    ],
                                    'headerOptions' => [
                                        'style' => 'width: 180px;',
                                        
                                    ] 
                            ],      
                        ]
                    ])->label('');?>
            </div>	
        </div>		
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

<?php
// ... sizning form kodingiz o‘zgarmaydi ...

$adviceUrl = \yii\helpers\Url::to(['product-category/sorting-advice']);
$js = <<<JS
(function(){
  var \$brand = $('#productcategory-brand_id');
  var \$sorting = $('#productcategory-sorting');
  var \$field = $('.field-productcategory-sorting');
  var \$hint = $('<div class="help-block text-info small" id="sorting-hint"></div>');
  if (\$field.find('#sorting-hint').length === 0) {
      \$field.append(\$hint);
  }

  function renderHint(data, currentVal) {
      if (!data) { \$hint.text(''); return; }
      var next = data.next_free || 1;
      var msg = 'Tavsiya etilgan keyingi raqam: ' + next;
      var v = parseInt(currentVal || 0, 10);
      if (v && v > next) {
          // kiritilgan qiymatgacha bo'shlar (oxirgi 5 tasini ko'rsatamiz)
          var miss = (data.missing || []).filter(function(n){ return n < v; });
          if (miss.length) {
              var tail = miss.slice(-5).join(', ');
              msg += ' — Diqqat: quyi bo‘sh raqam(lar): ' + tail;
          }
      }
      \$hint.text(msg);
  }

  var tId;
  function refreshHint() {
      var b = \$brand.val();
      if (!b) { \$hint.text('Avval brandni tanlang'); return; }
      $.getJSON('$adviceUrl', { brand_id: b, value: \$sorting.val() }, function(resp){
          renderHint(resp, \$sorting.val());
          // Agar sorting bo'sh bo'lsa, avtomatik next_free bilan to'ldirib beramiz (ixtiyoriy)
         
      });
  }

  // Brand Select2 bo'lsa ham ishlaydi
  \$brand.on('change', refreshHint);
  \$sorting.on('keyup change', function(){
      clearTimeout(tId);
      tId = setTimeout(refreshHint, 200);
  });

  // Form ochilganda bir marta
  refreshHint();
})();
JS;

$this->registerJs($js);
?>

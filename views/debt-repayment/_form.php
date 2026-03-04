<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use unclead\multipleinput\MultipleInput;
/* @var $this yii\web\View */
/* @var $model app\models\OrderAccount */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="/order-account/index" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
            <a href="javascript:;" title="Во весь экран" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" title="Обновить" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title"><?= $orderAccount->client->fio ?> ning qarzini to'lash</h4>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'total_debt')->textInput(['type' => 'number', 'value' => $total_debt, 'disabled' => true]) ?> 
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => Yii::t('app','Sanani tanlang...'), 'required'=>True, 'value' => date('d.m.Y')],
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose'=>true, 
                            'format' => 'dd.mm.yyyy',
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'exchange_rate')->textInput(['type' => 'number', 'required'=>True]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'discount_amount')->textInput(['type' => 'number', 'value' => 0,]) ?> 
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'summ_dollar')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'sum_som')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'summ_cart')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'sum_transfers')->textInput(['type' => 'number', 'value' => 0]) ?>
                </div>
            </div>
            <?php if (!Yii::$app->request->isAjax){ ?>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'To\'lash' : 'O\'zgartirish', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'style' => 'width:100%']) ?>
                </div>
            <?php } ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJsFile('/js/cookie.js');

$this->registerJs(<<<JS

$('#cars').on('change', function(e){
    e.preventDefault();
    const value = $(this).val().toUpperCase();
    console.log("value:", value);
    if (value) {
        $("#showRes .handle").filter(function() {
        const td = $(this).children('td').eq(1).text().toUpperCase();
        $(this).toggle(td === value)
        });

        $("#showRes .handle-header").filter(function() {
            const td = $(this).children('td').eq(0).text().toUpperCase();
            $(this).toggle(td === value)
        });

        $("#showRes tr").filter(function() {
            const className = $(this).attr('class').toUpperCase();
            if(className.indexOf('AMOUNT') > -1){
                $(this).toggle(className == value + '-AMOUNT')
            }
        });
    }else{
        $("#showRes tr").filter(function() {
        $(this).show()
        });
    }
});

JS
) ?>


<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use unclead\multipleinput\MultipleInput;
use app\models\ExchangeRate;
use app\models\MyTotalDebt;
/* @var $this yii\web\View */
/* @var $model app\models\Warehouse */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Tovar qo\'shish';
$this->params['breadcrumbs'][] = $this->title;
$exchangeRate = ExchangeRate::findOne(1);

?>

<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="/warehouse/index" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
            <a href="javascript:;" title="Во весь экран" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" title="Обновить" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Tovar qo'shish</h4>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-4" style="display:none;">
                    <?= $form->field($model, 'consignor_id')->textInput(['type' => 'number', 'value' => 31, 'style' => 'display:none;'])->label("") ?>
                    <!-- < ?= $form->field($model, 'consignor_id')->label()->widget(\kartik\select2\Select2::classname(), [
                        'data' => $model->getConsignor(),
                        'options' => [
                            'placeholder' => Yii::t('app','Tanlang...'),
                            'value' => 17,
                            'style' => 'display:none;',
                            'onchange'=>'
                                $.post( "/sklad/qarzs?id='.'"+$(this).val(), function( data ){
                                    $( "input#total_debts" ).val( data);
                                    alter(data);
                                });' 
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'allowClear' => true,
                        ],
                    ])->label('Yuk jo\'natuvchi <b style="color:red">(Kiritilish majburiy)</b>'); ?>  -->
                </div>
                <!-- <div class="col-md-3">
                    < ?php if (\Yii::$app->user->identity->permission == 1) {?>
                        < ?= $form->field($model, 'my_total_debts')->textInput(['id' => 'total_debts'])->label("Mening qarzim ($)") ?>
                    < ?php }else{?>
                        < ?= $form->field($model, 'my_total_debts')->textInput(['id' => 'total_debts', 'style' => 'display:none;'])->label("") ?>
                    < ?php }?>
                </div> -->
                <div class="col-md-3">
                    <?= $form->field($model, 'dates')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => Yii::t('app','Sanani tanlang...'), 'required'=>True, 'value' => date('d.m.Y')],
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose'=>true, 
                            'format' => 'dd.mm.yyyy',
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-2" style="display:none;">
                     <?php if (\Yii::$app->user->identity->permission == 1) {?>
                        <?= $form->field($model, 'exchange_rates')->textInput(['type' => 'number', 'required'=>True, 'value' => $exchangeRate->dollar])->label("Dollar kursi") ?>
                    <?php }else{?>
                        <?= $form->field($model, 'exchange_rates')->textInput(['type' => 'number', 'required'=>True, 'value' => $exchangeRate->dollar, 'style' => 'display:none;'])->label("") ?>
                    <?php }?>
                    
                </div>
                <div class="col-md-12">
                    <?php echo $form->field($model, 'allValue')->widget(MultipleInput::className(), [
                        'id' => 'my_id',
                        'columns' => [
                            [
                                'name' => 'brand_id',
                                'title' => 'Model',
                                'type' => \kartik\select2\Select2::className(),                                
                                'options' => [
                                    'data'  => $model->getBrands(),
                                    'options' => [
                                        'placeholder' => 'Tanlang...',
                                        'class' => 'input-priority',
                                        'required' => true
                                    ], 
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                                                     
                                ],
                                'headerOptions' => [
                                    'style' => 'width: 370px;',
                                ] 
                            ],
                            [
                                'name' => 'product_category_id',
                                'title' => 'Nomi',
                                'type' => \kartik\select2\Select2::className(),
                                'options' => [
                                    'data'  => $model->getProductCategories(),
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
                                        'style' => 'width: 370px;',
                                        
                                    ] 
                            ],
                            [
                                'name'  => 'size',
                                'title' => 'O\'lchami <b style="color:red">(Butun sonni nuqta bilan kiriting. Misol uchun: 9.99 )</b>',
                                'enableError' => true,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    // 'type' =>'number',
                                    'required' => true,
                                 ]
                            ],
                            [
                                'name' => 'type',
                                'title' => 'Tip',
                                'type' => \kartik\select2\Select2::className(),
                                'options' => [
                                    'data'  => $model->getProductDukonType(),
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
                                        'style' => 'width: 350px;',
                                        
                                    ] 
                            ],
                            // [
                            //     'name'  => 'count',
                            //     'title' => 'Soni',
                            //     'enableError' => true,
                            //     'options' => [
                            //         'type' =>'number',
                            //         'class' => 'input-priority target',
                            //         'headerOptions' => [
                            //             'style' => 'font-size: 40px',
                            //         ] ,
                            //     ]
                            // ],   
                            [
                                'name'  => 'price',
                                'title' => \Yii::$app->user->identity->permission == 1 ? 'Narxi ($)' : '',
                                'enableError' => true,
                                'options' => [ 
                                    // 'type' =>'number',
                                    'class' => 'input-priority',
                                    'style' => \Yii::$app->user->identity->permission == 1 ? '' : 'display: none;',
                                    'options' => [
                                        'id' => 'price',
                                    ], 
                                    'id' => 'price',
                                    'headerOptions' => [
                                        'id' => 'price',
                                    ] ,
                                    'pluginOptions' => [
                                        'id' => 'price',
                                    ],  
                                    // 'onchange'=>'
                                    //     price = $(this).val();
                                    //     // alert(price);
                                    // '
                                ]
                            ],    
                        ]
                    ])->label('');?>
                </div>
                <!-- <div class="row">
                    
                    <div class="col-md-10">
                    </div>
                    <div class="col-md-2">
                    < ?php if (\Yii::$app->user->identity->permission == 1) {?>
                        < ?= $form->field($model, 'sum_all_pro')->textInput([
                            'readonly' => true, // readonly qilib qo'yamiz
                            'style' => 'margin-top:-30px; margin-left:0px;width:170px',
                            'id' => 'sum_all_pro', // input elementga ID qo'yamiz
                        ]) ?>
                    < ?php }else{?>
                        < ?= $form->field($model, 'sum_all_pro')->textInput([
                            'readonly' => true, // readonly qilib qo'yamiz
                            'style' => 'margin-top:-30px; margin-left:0px;width:170px;display:none;',
                            'id' => 'sum_all_pro', // input elementga ID qo'yamiz
                        ])->label("") ?>
                    < ?php }?>
                        
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-10">
                        <?= $form->field($model, 'comments')->textInput(['required' => true])->label("<b style='color:red;'>Import bo'layotgan mahsulotlar bo'yicha izoh kiriting </b>") ?>
                    </div>
                    <!-- <div class="col-md-2">
                        < ?= $form->field($model, 'given_sum_dollars')->textInput(['type' => 'number', 'value' => 0, 'style' => 'display:none;'])->label("<b style='color:#1748d3'> </b>") ?> 
                    </div>
                    <div class="col-md-2">
                        < ?php if (\Yii::$app->user->identity->permission == 1) {?>
                            <?= $form->field($model, 'sum_dollars')->textInput(['type' => 'number', 'value' => 0])->label("<b style='color:#31701b'>Berilgan Summa ($) </b>") ?> 
                        < ?php }else{?>
                            <?= $form->field($model, 'sum_dollars')->textInput(['type' => 'number', 'value' => 0, 'style' => 'display:none;'])->label("<b style='color:#31701b'> </b>") ?> 
                        < ?php }?>
                    </div>
                    <div class="col-md-2">
                        < ?php if (\Yii::$app->user->identity->permission == 1) {?>
                            < ?= $form->field($model, 'discount_amounts')->textInput(['type' => 'number', 'value' => 0])->label("<b style='color:#f59c1a'>Jami chegirma ($) </b>") ?>
                        < ?php }else{?>
                            <?= $form->field($model, 'discount_amounts')->textInput(['type' => 'number', 'value' => 0, 'style' => 'display:none;'])->label("<b style='color:#f59c1a'> </b>") ?>
                        < ?php }?>
                        
                    </div> -->
                </div>
            <!-- <div class="row">
                <div class="col-md-3">
                    < ?= $form->field($model, 'order_account_statuses')->checkbox(['checked' => true])->label("<b style='font-size:16px;color:red'>Tasdiqlash: </b>") ?>
                </div>
            </div> -->
            </div>
            <?php if (!Yii::$app->request->isAjax){ ?>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Qo\'shish' : 'O\'zgartirish', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'style' => 'width:100%']) ?>
                </div>
            <?php } ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$this->registerJsFile('/js/cookie.js');

$this->registerJs(<<<JS

function copyToClipboard(element) {
    element.select(); // Element qiymatini tanlaymiz
    document.execCommand("copy"); // Nusxalaymiz

    // Xabar elementini yaratamiz
    const message = document.createElement("span");
    message.innerText = "Nusxalandi";
    message.style.position = "absolute";
    message.style.bottom = "50%";
    message.style.left = "150px";
    message.style.transform = "translateY(-50%)";
    message.style.backgroundColor = "#83a16ed9";
    message.style.color = "white";
    message.style.padding = "5px 10px";
    message.style.borderRadius = "4px";
    message.style.fontSize = "12px";

    // Xabarni input yoniga qo'shamiz
    element.parentNode.appendChild(message);

    // Xabarni 2 soniyadan keyin o'chiramiz
    setTimeout(() => {
        message.remove();
    }, 2000);
}

document.getElementById("sum_all_pro").onclick = function() {
    copyToClipboard(this); // Input ustiga bosilganda nusxalaymiz
};

var product_details = {};
$(document).on("change", ".input-priority", function() {
    const attr_name = $(this).attr('name');
    let id = attr_name.match(/\d/g).join("");
    
    const price = parseFloat($("input[name='Sklad[allValue][" + id + "][price]']").val());
    const count = parseInt($("input[name='Sklad[allValue][" + id + "][count]']").val());
    
    if (price && count) {
        product_details[id] = price * count; // mavjud bo'lsa yangilanadi, bo'lmasa qo'shiladi
        // console.log('product_details:', product_details);
        
        // Umumiy qiymatni hisoblash
        const total_sum = Object.values(product_details).reduce((a, b) => a + b, 0);
        $("input[name='Sklad[sum_all_pro]']").val(total_sum);
    }
});


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



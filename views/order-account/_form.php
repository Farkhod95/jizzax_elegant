<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use unclead\multipleinput\MultipleInput;
use app\models\ExchangeRate;
/* @var $this yii\web\View */
/* @var $model app\models\OrderAccount */
/* @var $form yii\widgets\ActiveForm */
$exchangeRate = ExchangeRate::findOne(1);
?>
<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="/order-account-history/index" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
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
                <div class="col-md-3">
                    <?= $form->field($model, 'clients_id')->label()->widget(\kartik\select2\Select2::classname(), [
                        'data' => $model->getClients(),
                        'options' => [
                            'placeholder' => Yii::t('app','Tanlang...'),
                            'onchange'=>'
                                $.post( "/order-account/qarzs?id='.'"+$(this).val(), function( data ){
                                    $( "input#total_debts" ).val( data);
                                    alter(data);
                                });' 
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'allowClear' => true,
                        ],
                            
                    ])->label('Mijoz <b style="color:red">(Kiritilish majburiy)</b>'); ?> 
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'total_debts')->textInput(['value' => 0, 'id' => 'total_debts']) ?> 
                </div>
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
                <div class="col-md-3">
                    <?= $form->field($model, 'exchange_rates')->textInput(['type' => 'number', 'required'=>True, 'value' => $exchangeRate->dollar]) ?>
                </div>
            </div>
            <div class="col-md-12">
                    <?php echo $form->field($model, 'allValue')->widget(MultipleInput::className(), [
                        'id' => 'my_id',
                        'allowEmptyList' => true,
                        'enableGuessTitle' => true,
                        'columns' => [
                            [
                                'name' => 'type_sklad_id',
                                'title' => 'Joy',
                                'type' => \kartik\select2\Select2::className(),
                                'options' => [
                                    'data'  => $model->getTypeSklads(),
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
                                    'style' => 'width: 180px;',
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
                                        'style' => 'width: 180px;',
                                        
                                    ] 
                            ],
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
                            [
                                'name'  => 'count',
                                'title' => 'Soni',
                                'enableError' => true,
                                'options' => [
                                    'type' =>'number',
                                    'class' => 'input-priority target',
                                    'headerOptions' => [
                                        'style' => 'font-size: 40px',
                                    ] ,
                                ]
                            ],   
                            [
                                'name'  => 'price',
                                'title' => 'Narxi',
                                'enableError' => true,
                                'options' => [ 
                                    // 'type' =>'number',
                                    'class' => 'input-priority',
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
                            // [
                            //     'name'  => 'real_price',
                            //     'title' => 'Asl narxi',
                            //     'enableError' => true,
                            //     'defaultValue' => 0,
                            //     'options' => [
                            //         'class' => 'input-priority',
                            //         'type' =>'number',
                            //     ]
                            // ],  
                                     
                        ]
                    ])->label('');?>
            </div>
            <div class="row">
                <div class="col-md-9">
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'sum_all_pro')->textInput([ 'disabled' => true, 'style' => 'margin-top:-30px; margin-left:65px;width:170px']) ?> 
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'sum_dollars')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'discount_amounts')->textInput(['type' => 'number', 'value' => 0])->label("<b style='color:#f59c1a'>Jami chegirma ($) </b>") ?>
                </div>
                
                <!-- <div class="col-md-2">
                    ?= $form->field($model, 'sum_soms')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-1">
                    ?= $form->field($model, 'dollar_sumdas')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-2">
                    ?= $form->field($model, 'sum_carts')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-2">
                    ?= $form->field($model, 'sum_transferss')->textInput(['type' => 'number', 'value' => 0]) ?>
                </div> -->
                <!-- <div class="col-md-2">
                    ?= $form->field($model, 'order_account_statuses')->checkbox(['checked' => true])->label("<b style='font-size:16px;color:red'>Tasdiqlash: </b>") ?>
                </div> -->
            </div>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'order_account_statuses')->checkbox(['checked' => true])->label("<b style='font-size:16px;color:red'>Tasdiqlash: </b>") ?>
                </div>
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

var product_details = {};
$(document).on("change", ".input-priority", function() {
    const attr_name = $(this).attr('name');
    let id = attr_name.match(/\d/g).join("");
    
    const price = parseFloat($("input[name='OrderAccount[allValue][" + id + "][price]']").val());
    const count = parseInt($("input[name='OrderAccount[allValue][" + id + "][count]']").val());
    
    if (price && count) {
        product_details[id] = price * count; // mavjud bo'lsa yangilanadi, bo'lmasa qo'shiladi
        console.log('product_details:', product_details);
        
        // Umumiy qiymatni hisoblash
        const total_sum = Object.values(product_details).reduce((a, b) => a + b, 0);
        $("input[name='OrderAccount[sum_all_pro]']").val(total_sum);
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


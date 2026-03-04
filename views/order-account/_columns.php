<?php
use yii\helpers\Url;
use app\models\Client;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'client_id',
        'width' => '280px',
        'filter' => ArrayHelper::map(Client::find()->all(),'id','fio'),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->client) {
                return '<b style="font-size: 14px">'.$data->client->fio.'</b>';
            }

            // client topilmasa, bo'sh yoki fallback
            return '<span class="text-muted">Mijoz topilmadi</span>';
            
        },
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'number_of_orders',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_product_sum',
        'content'=> function($data){
            if ($data->all_product_sum) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_product_sum, 2).' $</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_summ_dollar',
        'width' => '180px',
        // 'format'=>['decimal',2],
        'content'=> function($data){
            if ($data->all_summ_dollar) {
                return   $data->all_summ_dollar?'<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_summ_dollar,2).' $</b>':'';
            }
        }
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'sum_som',
    //     'width' => '180px',
    //     // 'format'=>['decimal',2],
    //     'content'=> function($data){
    //         if ($data->sum_som) {
    //             return   $data->sum_som?'<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->sum_som,2).' $</b>':'';
    //         }
    //     }
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'discount_amount',
        'content'=> function($data){
            if ($data->discount_amount) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->discount_amount,2).' $</b>';
            }
        }
        
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'total_debt',
        'content'=> function($data){
            if ($data->total_debt) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->total_debt,2).' $</b>';
            }
        }
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_profit_dollar',
        'visible' => \Yii::$app->user->identity->isAdminRight(\Yii::$app->user->identity->id),
        'content'=> function($data){
            if ($data->all_profit_dollar) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_profit_dollar,2).' $</b>';
            }
        }
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'width'=>'150px',
        'attribute'=>'date',
        'content' => function ($data) {
            return \Yii::$app->formatter->asDate($data->date, 'php:d.m.Y');
        },
    ], 
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'exchange_rate',
    // ],
    
    
    
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'date_last_debt_payment',
    // ],
    
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'last_order_date',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'sum_som',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'sum_dollar',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'sum_cart',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'sum_transfers',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'cr_date',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '180px',
        'header' => 'Harakatlar',
        'template' => ' {leadPrice3} {leadPrice} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadPrice2' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/debt-repayment/create' , 'account_id' => $model->id]);
                    return Html::a('<span class="glyphicon glyphicon-usd"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Qarzni to\'lash','class'=>'btn btn-warning btn-xs']);
                }
            },
            'leadPrice3' => function ($url, $model) {
                if ($model->client) {
                    if(\Yii::$app->user->identity->permission == 1){
                        $url = Url::to(['/debt-repayment/client-debt' , 'client_id' => $model->client_id, 'customer_fio' => $model->client->fio]);
                        return Html::a('<span class="glyphicon glyphicon-transfer"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Qarzni to\'lash tarixi','class'=>'btn btn-primary btn-xs']);
                    }
                }
            },
            'leadPrice' => function ($url, $model) {
                $url = Url::to(['/order-account/products' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-fullscreen"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Barcha mahsulotlar','class'=>'btn btn-secondary btn-xs']);
            },
            'leadView' => function ($url, $model) {
                $url = Url::to(['/order-account/view' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'Ko\'rish','class'=>'btn btn-info btn-xs']);
            },
            'leadUpdate' => function ($url, $model) {
                $url = Url::to(['/order-account/update', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-success btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/order-account/one-delete', 'id' => $model->id]);
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-danger btn-xs']);
                }
            },
            'leadDeleteOld' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    // Create the button to trigger the modal
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', '#', [
                        'data-toggle' => 'modal',
                        'data-target' => '#deleteModal-'.$model->id, // Unique modal ID
                        'title' => 'O\'chirish',
                        'class' => 'btn btn-danger btn-xs',
                    ]) .
                    // Modal HTML
                    '<div class="modal fade" id="deleteModal-'.$model->id.'" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel-'.$model->id.'" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel-'.$model->id.'"><b style="font-size:16px;color:red">'.$model->client->fio.'</b> ning buyurtmasini oʻchirib tashlamoqchimisiz?</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Buyurtmani o\'chirishdan oldin izoh kiriting:</p>
                                    '.Html::beginForm(['/order-account/delete', 'id' => $model->id], 'post').'
                                    <div class="form-group">
                                        '.Html::input('text', 'delete_reason', '', ['class' => 'form-control', 'placeholder' => 'Sababini kiriting...', 'required' => true]).'
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Bekor qilish</button>
                                    '.Html::submitButton('O\'chirish', ['class' => 'btn btn-danger']).'
                                    '.Html::endForm().'
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            },
        ],
    ],

];   
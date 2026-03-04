<?php
use yii\helpers\Url;
use app\models\Client;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'client_id',
        'width' => '180px',
        'filter' => ArrayHelper::map(Client::find()->all(),'id','fio'),
        'content' => function ($data) {
            if ($data->client_id) {
                if ($data->total_debt) {
                    return '<b style="font-size: 14px">'.$data->client->fio.'</b>';
                }else{
                    return '<b style="color:red;font-size: 14px">'.$data->client->fio.'</b>';
                }
            }
            
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
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_product_sum, 2).'</b>';
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
                return   $data->all_summ_dollar?'<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_summ_dollar).'</b>':'';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'discount_amount',
        'content'=> function($data){
            if ($data->discount_amount) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->discount_amount).'</b>';
            }
        }
        
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'total_debt',
        'content'=> function($data){
            if ($data->total_debt) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->total_debt).'</b>';
            }
        }
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_profit_dollar',
        'content'=> function($data){
            if ($data->all_profit_dollar) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_profit_dollar).'</b>';
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
        'width' => '150px',
        'header' => 'Harakatlar',
        'template' => '{leadPrice}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadPrice' => function ($url, $model) {
                $url = Url::to(['/order-account-history/products' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-fullscreen"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Barcha mahsulotlar','class'=>'btn btn-secondary btn-xs']);
            },
            'leadView' => function ($url, $model) {
                $url = Url::to(['/order-account-history/view' , 'id' => $model->id, 'type' => 2]);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'Ko\'rish','class'=>'btn btn-info btn-xs']);
            },
            'leadUpdate' => function ($url, $model) {
                $url = Url::to(['/order-account-history/update', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-success btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/order-account-history/delete', 'id' => $model->id]);
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', $url, [
                        'role'=>'modal-remote','title'=>'O\'chirish','class'=>'btn btn-danger btn-xs' ,
                        'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                        'data-request-method'=>'post',
                        'data-toggle'=>'tooltip',
                        'data-confirm-title'=>'Ishonchingiz komilmi?',
                        'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz',
                    ]);
            },
        ],
    ],

];   
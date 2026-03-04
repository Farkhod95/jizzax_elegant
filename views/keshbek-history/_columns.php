<?php
use yii\helpers\Url;
use app\models\OrderAccountHistory;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'keshbek',
        'content' => function ($data) {
            return '<b style="color:green;font-size: 14px">'.$data->keshbek.' %</b>';
            
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'keshbek_sum',
        'content' => function ($data) {
            return '<b style="color:#f59c1a;font-size: 14px">'.$data->keshbek_sum.' $</b>';
            
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'order_account_history_id',
        'content' => function ($data) {
            $orderAccountHistory = OrderAccountHistory::find()->where(['id' => $data->order_account_history_id])->one();
            if ($orderAccountHistory) {
                return '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($orderAccountHistory->all_product_sum, 2).'</b>';
            }
            
        },
    ], 
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cr_date',
        'content' => function ($data) {
            return \Yii::$app->formatter->asDate($data->cr_date, 'php:d.m.Y');
        },
    ], 
 
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '120px',
        'header' => 'Harakatlar',
        'template' => '{leadView} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadView' => function ($url, $model) {
                if (!empty($model->order_account_history_id)){
                    $url = Url::to(['/order-account-history/products' , 'id' => $model->order_account_history_id, 'type' => 'index']);
                    return Html::a('<span class="glyphicon glyphicon-list"></span>', $url, ['data-toggle'=>'tooltip', 'title'=>'Buyurtmani ko\'rish','class'=>'btn btn-warning btn-xs', 'data-pjax' => 0]);
                }
            },
            'leadDelete' => function ($url, $model) {
                if (empty($model->order_account_history_id)){
                    $url = Url::to(['/keshbek-history/delete', 'id' => $model->id]);
                        return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', $url, [
                            'role'=>'modal-remote','title'=>'O\'chirish','class'=>'btn btn-danger btn-xs' ,
                            'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                            'data-request-method'=>'post',
                            'data-toggle'=>'tooltip',
                            'data-confirm-title'=>'Ishonchingiz komilmi?',
                            'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz?',
                        ]);
                }
            },
        ],
    ],

];   
<?php
use yii\helpers\Url;
use app\models\Brands;
use app\models\ProductCategory;
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
        'attribute'=>'order_number',
        'content' => function ($data) {
            if ($data->order_number) {
                return '<b style="color:#f94877;font-size: 14px">'.$data->order_number.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'customer_fio',
        'content' => function ($data) {
            if ($data->customer_fio) {
                return '<b style="font-size: 14px">'.$data->customer_fio.'</b>';
            }
        },
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_by',
        'width' => '250px',
        'content' => function ($data) {
            if ($data->created_by) {
                return '<b style="font-size: 14px";>' . $data->createdBy->getFio(). '</b>';
            }
        },
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        // 'width'=>'200px',
        'attribute'=>'cr_date_time',
        'content' => function ($data) {
            return '<b style="font-size: 14px"s>' . $data->cr_date_time. '</b>';
        },
    ], 

    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'width' => '120px',
        'template' => '{leadPrint} {leadView} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadPrint' => function ($url, $model) {
                $url = Url::to(['/orders/print', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [ 'data-pjax' => 0, 'target' =>'_blank', 'data-toggle'=>'tooltip', 'title'=>'Chop qilish','class'=>'btn btn-warning btn-xs']);
            },
            'leadView' => function ($url, $model) {
                $url = Url::to(['/orders/products', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Tovarlar','class'=>'btn btn-success btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/orders/delete', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Buyurtmani bekor qilish','class'=>'btn btn-danger btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
               
                if(Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/orders/delete', 'id' => $model->id]);
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', $url, [
                        'role'=>'modal-remote','title'=>'Buyurtmani bekor qilish','class'=>'btn btn-danger btn-xs' ,
                        'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                        'data-request-method'=>'post',
                        'data-toggle'=>'tooltip',
                        'data-confirm-title'=>'Ishonchingiz komilmi?',
                        'data-confirm-message'=>'Haqiqatan ham shu buyurtmani bekor qilmoqchimisiz?',
                    ]);
                }
            },
        ],
    ],

];   
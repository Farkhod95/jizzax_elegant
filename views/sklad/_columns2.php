<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_by',
        'width' => '300px',
        'content' => function ($data) {
            if ($data->created_by) {
                return '<b style="font-size: 14px";>' . $data->createdBy->getFio(). '</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'consignor_id',
        'width' => '250px',
        'filter' => false,
        'content' => function ($data) {
            if ($data->consignor_id) {
                return '<b style="font-size: 14px">'.$data->consignor0->name.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        // 'width'=>'200px',
        'attribute'=>'cr_date',
        'content' => function ($data) {
            return '<b style="font-size: 14px"s>' . $data->cr_date. '</b>';
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        // 'width'=>'200px',
        'attribute'=>'cr_date_time',
        'content' => function ($data) {
            return '<b style="font-size: 14px">' . date("H:i:s", strtotime($data->cr_date_time)) . '</b>';
        },
    ], 
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'vAlign'=>'large',
        'template' => '{leadView}',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadPrint' => function ($url, $model) {
                $url = Url::to(['/orders/print', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [ 'data-pjax' => 0, 'target' =>'_blank', 'data-toggle'=>'tooltip', 'title'=>'Chop qilish','class'=>'btn btn-warning btn-xs']);
            },
            'leadView' => function ($url, $model) {
                $url = Url::to(['/sklad/sklad-view2', 'id' => $model->id, 'cr_date' => $model->cr_date ]);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Tovarlar','class'=>'btn btn-success btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/orders/delete', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Buyurtmani bekor qilish','class'=>'btn btn-danger btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/orders/delete', 'id' => $model->id]);
                if($model->id != \app\models\Users::SUPER_ADMIN_ID){
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
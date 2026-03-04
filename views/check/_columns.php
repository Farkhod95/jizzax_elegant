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
        'attribute'=>'test',
        'width' => '500px',
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
        'class'=>'\kartik\grid\DataColumn',
        // 'width'=>'200px',
        'attribute'=>'status',
        'content' => function ($data) {
            return '<b style="font-size: 14px; color:green"><span class="glyphicon glyphicon-check"></span> Tekshirildi </b>';
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
                $url = Url::to(['check/all-list' , 'id' => $model->id, 'date' => $model->cr_date]);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['data-toggle'=>'tooltip', 'title'=>'Mahsulotlar','class'=>'btn btn-warning btn-xs', 'data-pjax' => 0]);
            },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/check/delete', 'id' => $model->id]);
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', $url, [
                        'role'=>'modal-remote','title'=>'O\'chirish','class'=>'btn btn-danger btn-xs' ,
                        'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                        'data-request-method'=>'post',
                        'data-toggle'=>'tooltip',
                        'data-confirm-title'=>'Ishonchingiz komilmi?',
                        'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz?',
                    ]);
            },
        ],
    ],


];   
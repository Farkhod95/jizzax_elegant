<?php
use yii\helpers\Url;
use yii\helpers\Html;
return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'profit',
        'content' => function ($data) {
            if ($data->profit) {
                return '<b style="font-size: 14px; color:green";>' . $data->profit. '</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'loss',
        'content' => function ($data) {
            if ($data->loss) {
                return '<b style="font-size: 14px; color:red";>' . $data->loss. '</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'width'=>'200px',
        'attribute'=>'date_start',
        'content' => function ($data) {
            return \Yii::$app->formatter->asDate($data->date_start, 'php:d.m.Y');
        },
    ], 
    [
        'class'=>'\kartik\grid\DataColumn',
        'width'=>'200px',
        'attribute'=>'date_end',
        'content' => function ($data) {
            return \Yii::$app->formatter->asDate($data->date_end, 'php:d.m.Y');
        },
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
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'width' => '120px',
        'template' => '{leadView} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadView' => function ($url, $model) {
                $url = Url::to(['/loss-of-profit/view', 'start_date' => $model->date_start, 'end_date' => $model->date_end]);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Tovarlar','class'=>'btn btn-success btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/loss-of-profit/delete', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Buyurtmani bekor qilish','class'=>'btn btn-danger btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
               
                if(Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/loss-of-profit/delete', 'id' => $model->id]);
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
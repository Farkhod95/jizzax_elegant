<?php
use yii\helpers\Url;
use app\models\TypeExpense;
use yii\helpers\ArrayHelper;
return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'nomi',
        'content'=> function($data){
            if ($data->nomi) {
                return   '<b style="font-size: 14px">'.$data->nomi.'</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type_id',
        'filter' => ArrayHelper::map(TypeExpense::find()->all(),'id','name'),
        'content'=> function($data){
            if($data->type_id){
                return '<b style="font-size: 14px">'.$data->type->name.'</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'summa',
        'content'=> function($data){
            if ($data->summa) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->summa,0).'</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'width'=>'150px',
        'attribute'=>'date_cr',
        'content' => function ($data) {
            return '<b style="font-size: 14px">'.\Yii::$app->formatter->asDate($data->date_cr, 'php:d.m.Y').'</b>';
        },
    ],
    
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'loss_of_profit_id',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'vAlign'=>'middle',
        'template' => '{update} {delete}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'Ko\'rish','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'O\'zgartirish', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'O\'chirish', 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Ishonchingiz komilmi?',
                          'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz?'], 
    ],

];   
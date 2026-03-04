<?php
use yii\helpers\Url;
use app\models\Client;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use app\models\ElegantHistoryUpdate;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
        'width' => '200px',
        'filter' => ElegantHistoryUpdate::getType(),
        'content' => function($model){
            if ($model->type == 1) {
                return '<b style="font-size: 14px;color:green" >'. $model->getTypeView($model->type) .'</b>';
            }else{
                return '<b style="font-size: 14px;color:red" >'. $model->getTypeView($model->type) .'</b>';
            }
            
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'title',
        // 'width' => '400px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
        'width' => '200px',
        'filter' => ElegantHistoryUpdate::getStatus(),
        'content' => function($model){
            return '<b style="font-size: 14px;color:#f59c1a" >'. $model->getStatusView($model->status) .'</b>';
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'comment',
        'format' => 'raw', // HTML teglarni ko'rsatish uchun 'raw' formatini qo'llaymiz
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'created_by',
        'width' => '250px',
        'filter' => ArrayHelper::map( User::find()
            ->select(['id', new \yii\db\Expression("CONCAT(surname, ' ', name) AS full_name")])
            ->asArray()
            ->all(),
            'id',
            'full_name' // This will use the concatenated full name for filtering
        ),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->created_by) {
                return '<b style="font-size: 14px">'.$data->createdBy->surname.' '.$data->createdBy->name.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'width'=>'150px',
        'attribute'=>'cr_date',
        'content' => function ($data) {
            if ($data->cr_date) {
                $dateTime = new DateTime($data->cr_date);
                return $dateTime->format('d.m.Y H:i:s');
            }
        },
    ], 
    // [
    //     'class' => 'kartik\grid\ActionColumn',
    //     'dropdown' => false,
    //     'vAlign'=>'middle',
    //     'urlCreator' => function($action, $model, $key, $index) { 
    //             return Url::to([$action,'id'=>$key]);
    //     },
    //     'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
    //     'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
    //     'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete', 
    //                       'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
    //                       'data-request-method'=>'post',
    //                       'data-toggle'=>'tooltip',
    //                       'data-confirm-title'=>'Are you sure?',
    //                       'data-confirm-message'=>'Are you sure want to delete this item'], 
    // ],

];   
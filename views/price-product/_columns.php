<?php
use yii\helpers\Url;
use app\models\ProductCategory;
use app\models\Brands;
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
        'attribute'=>'brand_id',
        'width' => '250px',
        'filter' => ArrayHelper::map(Brands::find()->where(['sup_status' => 1])->all(),'id','name'),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            return '<b style="font-size: 14px">'.$data->brand->name.'</b>';
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'product_category_id',
        'width' => '250px',
        // 'filter' => ArrayHelper::map(ProductCategory::find()->where(['sup_status' => 1])->all(),'id','name'),
        'filter' => ArrayHelper::map(ProductCategory::find()->where(['sup_status' => 1])->all(),'id','name'),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            return '<b style="font-size: 14px">'.$data->productCategory->name.'</b>';
        },
        
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'size',
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'type',
        // 'width' => '250px',
        'content' => function ($data) {
            if ($data->type) {
                return '<b style="font-size: 14px">'.$data->getTypeView($data->type).'</b>';
            }
            
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'real_price',
        'visible' => \Yii::$app->user->identity->permission == 1 ? true : false,
        'content' => function ($data) {
            return '<b style="font-size: 14px">'.$data->real_price.'</b>';
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cr_date',
        'content' => function ($data) {
            return '<b style="font-size: 14px">'.\Yii::$app->formatter->asDate($data->cr_date, 'php:d.m.Y').'</b>';
        },
    ],
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
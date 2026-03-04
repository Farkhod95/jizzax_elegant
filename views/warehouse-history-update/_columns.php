<?php
use yii\helpers\Url;
use app\models\Brands;
use app\models\ProductCategory;
use yii\helpers\ArrayHelper;
use app\models\Warehouse;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'brand_id',
        'width' => '150px',
        'filter' => ArrayHelper::map(Brands::find()->all(),'id','name'),
        'content' => function ($data) {
            return '<b style="font-size: 14px">'.$data->brand->name.'</b>';
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'product_category_id',
        'width' => '150px',
        'filter' => ArrayHelper::map(ProductCategory::find()->all(),'id','name'),
        'content' => function ($data) {
            return '<b style="font-size: 14px" >'.$data->productCategory->name.'</b>';
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'size',
        'content' => function ($data) {
            if ($data->size) {
                return '<b style="font-size: 14px">'.$data->size.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
        'width' => '200px',
        'filter' => Warehouse::getProductType(),
        'content' => function($model){
            if ($model->type) {
                return '<b style="font-size: 14px" >'. $model->getProductTypeView($model->type) .'</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'count',
        'content' => function ($data) {
            if ($data->count) {
                return '<b style="color:#f59c1a; font-size: 14px">'.$data->count.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'count_update',
        'content' => function ($data) {
            if ($data->count_update) {
                return '<b style="color:red; font-size: 14px">'.$data->count_update.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'update_by',
        'width' => '250px',
        'value' => function ($data) {
            if ($data->update_by) {
                return $data->updateBy->getFio();
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cr_date',
        'content' => function ($data) {
            if ($data->cr_date) {
                return '<b style="font-size: 14px">'.$data->cr_date.'</b>';
            }
        },
    ],
    
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'vAlign'=>'middle',
        'template' => '{view} {delete}',
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
                          'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz'], 
    ],

];   
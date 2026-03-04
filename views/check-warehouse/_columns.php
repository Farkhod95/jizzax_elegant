<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\Brands;
use app\models\ProductCategory;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'brand_id',
        'width' => '250px',
        'filter' => ArrayHelper::map(Brands::find()->all(),'id','name'),
        'content' => function ($data) {
            if ($data->count) {
                return '<b style="font-size: 14px">'.$data->brand->name.'</b>';
            }else{
                return '<b style="color:red;font-size: 14px">'.$data->brand->name.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'product_category_id',
        'width' => '250px',
        'filter' => ArrayHelper::map(ProductCategory::find()->all(),'id','name'),
        'content' => function ($data) {
            if ($data->count) {
                return '<b style="font-size: 14px">'.$data->productCategory->name.'</b>';
            }else{
                return '<b style="color:red;font-size: 14px">'.$data->productCategory->name.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'size',
        'content' => function ($data) {
            if ($data->size) {
                if ($data->count) {
                    return '<b style="font-size: 14px">'.$data->size.'</b>';
                }else{
                    return '<b style="color:red;font-size: 14px">'.$data->size.'</b>';
                }
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'count',
        'content' => function ($data) {
            if ($data->count) {
                return '<b style="font-size: 14px">'.$data->count.'</b>';
            }else{
                return '<b style="color:red;font-size: 14px">0</b>';
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
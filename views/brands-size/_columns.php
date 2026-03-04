<?php
use yii\helpers\Url;
use app\models\Brands;
use app\models\ProductCategory;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use app\models\BrandsSize;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],

    // BRAND (Select2, ID: #filter-brand)
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'brand_id',
        'width' => '500px',
        'filter' => \yii\helpers\ArrayHelper::map(
            \app\models\Brands::find()
                ->where(['sup_status' => 1])               // <<< qo'shildi
                ->orderBy(['sorting'=>SORT_ASC,'name'=>SORT_ASC])
                ->all(),
            'id','name'
        ),
        'filterType' => GridView::FILTER_SELECT2,
        'filterInputOptions' => [
            'id' => 'filter-brand',
        ],
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => [
                'id' => 'filter-brand',
                'data-placeholder' => Yii::t('app','Tanlang...'),
                'prompt' => Yii::t('app','Tanlang...'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => false,
                'placeholder' => Yii::t('app','Tanlang...'),
            ],
        ],
        'format' => 'raw',
        'content' => function ($data) {
            if ($data->brand_id && $data->brand) {
                return '<b style="font-size:14px">'.$data->brand->name.'</b>';
            }
            return '';
        },
    ],

    // CATEGORY (Select2, ID: #filter-category) — brand ga bog‘liq
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'product_category_id',
        'width' => '500px',
        'filter' => \yii\helpers\ArrayHelper::map(
            \app\models\ProductCategory::find()
                ->alias('pc')
                ->innerJoin(['b' => \app\models\Brands::tableName()], 'b.id = pc.brand_id')
                ->select(['pc.id','pc.name','b.name AS brand_name'])
                ->where(['pc.sup_status' => 1, 'b.sup_status' => 1]) // <<< ikkalasiga ham
                ->orderBy(['b.name'=>SORT_ASC, 'pc.name'=>SORT_ASC])
                ->asArray()
                ->all(),
            'id',
            function($row){
                return !empty($row['brand_name'])
                    ? "{$row['name']} ({$row['brand_name']})"
                    : $row['name'];
            }
        ),
        'filterType' => GridView::FILTER_SELECT2,
        'filterInputOptions' => [
            'id' => 'filter-category',
        ],
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => [
                'id' => 'filter-category',
                'data-placeholder' => Yii::t('app','Tanlang...'),
                'prompt' => Yii::t('app','Tanlang...'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => false,
                'placeholder' => Yii::t('app','Tanlang...'),
            ],
        ],
        'format' => 'raw',
        'content' => function ($data) {
            if ($data->product_category_id && $data->productCategory) {
                return '<b style="font-size:14px">'.$data->productCategory->name.'</b>';
            }
            return '';
        },
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'width' => '280px',
        'attribute'=>'size',
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
        'filter' => BrandsSize::getType(),
        'format' => 'raw',
        'content' => function ($data) {
            if ($data->type) {
                return '<b style="font-size:14px">'.$data->getTypeView($data->type).'</b>';
            }
            return '';
        },
    ],

    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'template' => '{update} {delete}',
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>"Ko'rish",'data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>"O'zgartirish", 'data-toggle'=>'tooltip'],
        'deleteOptions'=>[
            'role'=>'modal-remote','title'=>"O'chirish",
            'data-confirm'=>false, 'data-method'=>false,
            'data-request-method'=>'post',
            'data-toggle'=>'tooltip',
            'data-confirm-title'=>'Ishonchingiz komilmi?',
            'data-confirm-message'=>"Haqiqatan ham bu elementni o‘chirib tashlamoqchimisiz",
        ],
    ],
];

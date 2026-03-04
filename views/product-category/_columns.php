<?php
use yii\helpers\Url;
use app\models\Brands;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;

return [
    // [
    //     'class' => 'kartik\grid\SerialColumn',
    //     'width' => '30px',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'sorting',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'brand_id',
        'width' => '280px',
        'filter' => ArrayHelper::map(Brands::find()->all(),'id','name'),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->brand_id) {
                return '<b style="font-size: 14px">'.$data->brand->name.'</b>';
            }
            
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
        'content' => function ($data) {
            return '<b style="font-size: 14px" >'.$data->name.'</b>';
        },
    ],
     [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '180px',
        'header' => 'Harakatlar',
        'template' => '{leadViewSize} {leadEdit} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadViewSize' => function ($url, $model) {
                $url = Url::to(['/product-category/size-list' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-list"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'lchamlar','class'=>'btn btn-info btn-xs']);
            },
            'leadEdit' => function ($url, $model) {
                $url = Url::to(['/product-category/update', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-pencil "></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-warning btn-xs', 'target' => '_blank']);
            },
            'leadDelete' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                $url = Url::to(['/product-category/delete', 'id' => $model->id]);
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', $url, [
                        'role'=>'modal-remote','title'=>'O\'chirish','class'=>'btn btn-danger btn-xs' ,
                        'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                        'data-request-method'=>'post',
                        'data-toggle'=>'tooltip',
                        'data-confirm-title'=>'Ishonchingiz komilmi?',
                        'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz',
                    ]);
                }
            },
        ],
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'sup_status',
    //     'width' => '30px',
    // ],
    // [
    //     'class' => 'kartik\grid\ActionColumn',
    //     'dropdown' => false,
    //     'header' => 'Harakatlar',
    //     'template' => '{update} {delete}',
    //     'vAlign'=>'middle',
    //     'urlCreator' => function($action, $model, $key, $index) { 
    //             return Url::to([$action,'id'=>$key]);
    //     },
    //     'viewOptions'=>['role'=>'modal-remote','title'=>'Ko\'rish','data-toggle'=>'tooltip'],
    //     'updateOptions'=>['role'=>'modal-remote','title'=>'O\'zgartirish', 'data-toggle'=>'tooltip'],
    //     'deleteOptions'=>['role'=>'modal-remote','title'=>'O\'chirish', 
    //                       'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
    //                       'data-request-method'=>'post',
    //                       'data-toggle'=>'tooltip',
    //                       'data-confirm-title'=>'Ishonchingiz komilmi?',
    //                       'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz'], 
    // ],

];   
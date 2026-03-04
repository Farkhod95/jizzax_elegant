<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\MyTotalDebt;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'phone',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'my_all_total_debt',
        'width' => '140px',
        'content'=> function($data){
            $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $data->id])->one();
            if ($myTotalDebt) {
                return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($myTotalDebt->total_debt??0, 2).' $</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'address',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '150px',
        'header' => 'Harakatlar',
        'template' => '{leadAllProducts} {leadPrice} {leadUpdate} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadAllProducts' => function ($url, $model) {
                $url = Url::to(['/consignor/products' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-resize-full"></span>', $url, ['data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Barcha tavarlar ro\'yxati','class'=>'btn btn-info btn-xs']);
            },
            'leadPrice' => function ($url, $model) {
                $url = Url::to(['/sklad/products' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-shopping-cart"></span>', $url, ['data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Kelgan yuklar ro\'yxati','class'=>'btn btn-warning btn-xs']);
            },
            'leadUpdate' => function ($url, $model) {
                $url = Url::to(['/consignor/update', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-success btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/consignor/delete', 'id' => $model->id]);
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', $url, [
                        'role'=>'modal-remote','title'=>'O\'chirish','class'=>'btn btn-danger btn-xs' ,
                        'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                        'data-request-method'=>'post',
                        'data-toggle'=>'tooltip',
                        'data-confirm-title'=>'Ishonchingiz komilmi?',
                        'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz',
                    ]);
            },
        ],
    ],

];   
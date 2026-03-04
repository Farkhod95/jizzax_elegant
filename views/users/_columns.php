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
        'attribute'=>'avatar',
        'width' => '60px',
        'content' => function($model){
            return '<img src="'.$model->getAvatar().'" style="height:50px; border-radius:20%;">';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
        'label' => 'FIO',
        'content' => function($model){
            return '<b style="font-size: 14px" >'. $model->getFio().'</b>';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'username',
        'content' => function ($data) {
            return '<b style="font-size: 14px" >'.$data->username.'</b>';
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'address',
        'content' => function ($data) {
            return '<b style="font-size: 14px" >'.$data->address.'</b>';
        },
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'status',
    //     'filter' =>\app\models\Users::getStatus(),
    //     'content' => function($model){
    //         return $model->getStatusDescription();
    //     }
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'permission',
        'filter' =>\app\models\Users::getRole(),
        'content' => function($model){
            return '<b style="font-size: 14px" >'. $model->getRoleDescription() .'</b>';
        }
    ],

    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'avatar',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'surname',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'name',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'middle_name',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'phone',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'last_seen',
        'content' => function ($data) {
            return '<b style="font-size: 14px" >'.$data->last_seen.'</b>';
        },
    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'access_token',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'registry_date',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'email_verified',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'phone_verified',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'referal_id',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'vAlign'=>'middle',
        'template' => '{leadUpdate} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadUpdate' => function ($url, $model) {
                $url = Url::to(['/users/update', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-success btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/users/delete', 'id' => $model->id]);
                if($model->id != \app\models\Users::SUPER_ADMIN_ID){
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

];   
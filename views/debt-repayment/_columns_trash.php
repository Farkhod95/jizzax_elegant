<?php
use yii\helpers\Url;
use app\models\Client;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use app\models\User;


return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'client_id',
        'width' => '280px',
        'filter' => ArrayHelper::map(Client::find()->all(),'id','fio'),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content'=> function($data){
            if($data->client_id){
                return $data->client->fio;
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'created_by',
        'width' => '250px',
        'filter' => ArrayHelper::map(
        User::find()
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
            if ($data->date) {
                return   '<b style="font-size: 14px;color:#f59c1a">'.$data->createdBy->surname.' '. $data->createdBy->name.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'summ_dollar',
        'content'=> function($data){
            if ($data->summ_dollar) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->summ_dollar,2).'</b>';
            }
        }
        
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'sum_som',
    //     'content'=> function($data){
    //         if ($data->sum_som) {
    //             return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->sum_som,2).'</b>';
    //         }
    //     }
        
    // ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'summ_cart',
    //     'content'=> function($data){
    //         if ($data->summ_cart) {
    //             return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->summ_cart,2).'</b>';
    //         }
    //     }
        
    // ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'sum_transfers',
    //     'content'=> function($data){
    //         if ($data->sum_transfers) {
    //             return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->sum_transfers,2).'</b>';
    //         }
    //     }
        
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'discount_amount',
        'content'=> function($data){
            if ($data->discount_amount) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->discount_amount,2).'</b>';
            }
        }
        
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_summ_dollar',
        // 'width' => '180px',
        // 'format'=>['decimal',2],
        'content'=> function($data){
            if ($data->all_summ_dollar) {
                return   $data->all_summ_dollar?'<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_summ_dollar,2).'</b>':'';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'exchange_rate',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'width'=>'150px',
        'attribute'=>'date',
        'content' => function ($data) {
            return \Yii::$app->formatter->asDate($data->date, 'php:d.m.Y');
        },
    ], 
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '180px',
        'header' => 'Harakatlar',
        'template' => '{leadReturn} {leadView} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadView' => function ($url, $model) {
                $url = Url::to(['/debt-repayment/view' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'Ko\'rish','class'=>'btn btn-info btn-xs']);
            },
            'leadPrint' => function ($url, $model) {
                $url = Url::to(['/debt-repayment/print-debt', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Chop qilish','class'=>'btn btn-warning btn-xs', 'target' => '_blank']);
            },
            'leadTrash' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                $url = Url::to(['/debt-repayment/trash', 'id' => $model->id]);
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
            'leadReturn' => function ($url, $model) {
               
                if(Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/debt-repayment/return', 'id' => $model->id]);
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-refresh"></span>', $url, [
                        'role'=>'modal-remote','title'=>'Qarz to\'lovni tiklash','class'=>'btn btn-warning btn-xs' ,
                        'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                        'data-request-method'=>'post',
                        'data-toggle'=>'tooltip',
                        'data-confirm-title'=>'Ishonchingiz komilmi?',
                        'data-confirm-message'=>'Haqiqatan ham shu qarz to\'lovni qayta tiklamoqchimisiz?',
                    ]);
                }
            },
            'leadDelete' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                $url = Url::to(['/debt-repayment/delete', 'id' => $model->id]);
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
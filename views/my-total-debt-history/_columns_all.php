<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use app\models\MyTotalDebt;
use app\models\User;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'my_total_debt_id',
        'label' => 'Yetkazib beruvchi',
        'width' => '250px',
        'filter' => ArrayHelper::map(MyTotalDebt::find()->joinWith('consignor')->all(), 'id', function ($model) {
            return $model->consignor ? $model->consignor->name : '';
        }),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content'=> function($data){
            return $data->myTotalDebt && $data->myTotalDebt->consignor
                ? '<b style="font-size: 14px;" >'. $data->myTotalDebt->consignor->name .'</b>'
                : '';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_summ_dollar',
        'content'=> function($data){
            return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->all_summ_dollar?: 0, 2) .' $</b>';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'discount_amount',
        'content'=> function($data){
            return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->discount_amount?: 0, 2) .' $</b>';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'exchange_rate',
        'content'=> function($data){
            return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->exchange_rate?: 0, 2) .' $</b>';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cr_date',
        'content' => function ($data) {
            return '<b style="font-size: 14px;" >'. \Yii::$app->formatter->asDate($data->cr_date, 'php:d.m.Y') .'</b>';
        },
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
            'full_name'
        ),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->created_by) {
                return '<b style="font-size: 14px;" >'. $data->createdBy->surname.' '.$data->createdBy->name .'</b>';
            }
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '180px',
        'header' => 'Harakatlar',
        'template' => '{leadPrint} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadPrint' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission != 1){
                    return '';
                }
                $url = Url::to(['/my-total-debt-history/print', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Chop qilish','class'=>'btn btn-warning btn-xs', 'target' => '_blank']);
            },
            'leadDelete' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission != 1){
                    return '';
                }
                $url = Url::to(['/my-total-debt-history/delete', 'id' => $model->id]);
                return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', $url, [
                    'role'=>'modal-remote','title'=>'O\'chirish','class'=>'btn btn-danger btn-xs' ,
                    'data-confirm'=>false, 'data-method'=>false,
                    'data-request-method'=>'post',
                    'data-toggle'=>'tooltip',
                    'data-confirm-title'=>'Ishonchingiz komilmi?',
                    'data-confirm-message'=>'Haqiqatan ham bu elementni o\'chirib tashlamoqchimisiz',
                ]);
            },
        ],
    ],
];

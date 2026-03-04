<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Regions;
use app\models\Districts;
use app\models\OrderAccount;
use yii\helpers\Html;
use app\models\User;
use kartik\grid\GridView;
use kartik\select2\Select2;
use app\models\KeshbekHistory;


return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fio',
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'total_debt',
        'content'=> function($data){
            $orderAccount = OrderAccount::find()->where(['client_id' => $data->id])->one();
            if($orderAccount){
                return   '<b style="color:red;font-size: 14px">'.Yii::$app->formatter->asDecimal($orderAccount->total_debt??0, 2).' $</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'phone',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        // 'width'=>'200px',
        'attribute'=>'region_id',
        'filter' => ArrayHelper::map(Regions::find()->all(),'id','name'),
        'content'=> function($data){
            if($data->region_id){
                return $data->region->name;
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        // 'width'=>'200px',
        'attribute'=>'district_id',
        'filter' => ArrayHelper::map(Districts::find()->all(),'id','name'),
        'content'=> function($data){
            if($data->district_id){
                return $data->district->name;
            }
        }
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'address',
    // ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'keshbek',
    //     'width'=>'120px',
    //     'content'=> function($data){
    //         return   '<b style="color:green;font-size: 14px">'.($data->keshbek? $data->keshbek:0).' %</b>';
    //     }
    // ],
      [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'type',
        // 'width' => '250px',
        'filter' => (new \app\models\Client())->getType(),
        'content' => function ($data) {
            if ($data->type) {
                return '<b style="font-size: 14px">'.$data->getTypeView($data->type).'</b>';
            }
            
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'is_profit_loss',
        'visible' => \Yii::$app->user->identity->isAdminRight(\Yii::$app->user->identity->id),
        'width'=>'120px',
        'content'=> function($data){
            if ($data->is_profit_loss == 1) {
                return   '<b style="color:red;font-size: 14px"> Hisoblanmasin </b>';
            }else {
                return   '<b style="color:green;font-size: 14px">Hisoblansin</b>';
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
            if ($data->created_by) {
                return '<b style="font-size: 14px">'.$data->createdBy->surname.' '. $data->createdBy->name.'</b>';
            }
        },
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'header' => 'Keshbek ($)',
        'format' => 'raw',
        'value' => function($data){
            $sum = (float) KeshbekHistory::find()
                ->where(['client_id' => $data->id])
                ->sum('keshbek_sum');

            return '<b style="color:green;font-size:14px">'
                . Yii::$app->formatter->asDecimal($sum, 2)
                . ' $</b>';
        }
    ],
    // [
    //     'class' => 'kartik\grid\ActionColumn',
    //     'dropdown' => false,
    //     'vAlign'=>'middle',
    //     'header' => 'Harakatlar',
    //     'template' => '{update}',
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
    //                       'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz?'], 
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '180px',
        'header' => 'Harakatlar',
        'template' => '{leadKeshbek} {leadUpdate}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadKeshbek' => function ($url, $model) {
                $url = Url::to(['/keshbek-history/index' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-stats"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Keshbeklar','class'=>'btn btn-success btn-xs']);
            },
            'leadUpdate' => function ($url, $model) {
                $url = Url::to(['/client/update', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-warning btn-xs']);
            },
            'leadPercent' => function ($url, $model) {
                $url = Url::to(['/client/client-keshbek-hisob', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-usd  "></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Keshbek hisobi','class'=>'btn btn-info btn-xs']);
            },
        ],
    ],

];   
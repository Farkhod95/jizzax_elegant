<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\OrderAccount */
?>
<div class="order-account-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'class'=>'\kartik\grid\DataColumn', 
                'attribute'=>'client_id',
                'width' => '250px',
                'value' => function ($data) {
                    if ($data->client_id) {
                        return $data->client->fio;
                    }
                    
                },
            ],
            [
                'class'=>'\kartik\grid\DataColumn',
                'width'=>'150px',
                'attribute'=>'date',
                'value' => function ($data) {
                    if ($data->date) {return \Yii::$app->formatter->asDate($data->date, 'php:d.m.Y');}
                    
                },
            ], 
            'exchange_rate',
            'discount_amount',
            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'all_summ_dollar',
                // 'format'=>['decimal',2],
                'value'=> function($data){
                    return $data->all_summ_dollar?Yii::$app->formatter->asDecimal($data->all_summ_dollar,0):'';
                }
            ],
            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'all_profit_dollar',
                'visible' => \Yii::$app->user->identity->isAdminRight(\Yii::$app->user->identity->id),
                'value'=> function($data){
                    if ($data->all_profit_dollar) {
                        return Yii::$app->formatter->asDecimal($data->all_profit_dollar,0);
                    }
                }
            ],
            [
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'total_debt',
                'visible' => \Yii::$app->user->identity->isAdminRight(\Yii::$app->user->identity->id),
                'value'=> function($data){
                    if ($data->total_debt) {
                        return Yii::$app->formatter->asDecimal($data->total_debt,0);
                    }
                }
            ],
            // [
            //     'class'=>'\kartik\grid\DataColumn',
            //     'width'=>'150px',
            //     'attribute'=>'date_last_debt_payment',
            //     'value' => function ($data) {
            //         if ($data->date_last_debt_payment) return \Yii::$app->formatter->asDate($data->date_last_debt_payment, 'php:d.m.Y');
            //     },
            // ],
            'number_of_orders',
            // [
            //     'class'=>'\kartik\grid\DataColumn',
            //     'width'=>'150px',
            //     'attribute'=>'last_order_date',
            //     'value' => function ($data) {
            //         if ($data->last_order_date) return \Yii::$app->formatter->asDate($data->last_order_date, 'php:d.m.Y');
            //     },
            // ],
            'sum_som',
            'sum_dollar',
            'sum_cart',
            'sum_transfers',
            [
                'class'=>'\kartik\grid\DataColumn', 
                'attribute'=>'created_by',
                'width' => '250px',
                'value' => function ($data) {
                    if ($data->created_by) {
                        return $data->createdBy->fio;
                    }
                    
                },
            ],
            // [
            //     'class'=>'\kartik\grid\DataColumn',
            //     'width'=>'150px',
            //     'attribute'=>'cr_date',
            //     'value' => function ($data) {
            //         if ($data->cr_date) return \Yii::$app->formatter->asDate($data->cr_date, 'php:d.m.Y');
            //     },
            // ],
        ],
    ]) ?>

</div>

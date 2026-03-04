<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */
?>
<div class="orders-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'order_number',
            'customer_fio',
            'cr_date',
            [
                'attribute'=>'created_by',
                'width' => '250px',
                'value' => function ($data) {
                    if ($data->created_by) {
                        return $data->createdBy->getFio();
                    }
                },
            ],
        ],
    ]) ?>

</div>

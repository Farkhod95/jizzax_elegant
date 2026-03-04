<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\VozvratOrder */
?>
<div class="vozvrat-order-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'client_id',
            'date',
            'exchange_rate',
            'discount_amount',
            'summ_dollar',
            'all_summ_dollar',
            'total_debt',
            'confirmation',
            'cr_date_time',
            'comment:ntext',
            'created_by',
        ],
    ]) ?>

</div>

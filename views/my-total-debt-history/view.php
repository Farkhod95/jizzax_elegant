<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MyTotalDebtHistory */
?>
<div class="my-total-debt-history-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cr_date',
            'total_debt',
            'discount_amount',
            'my_total_debt_id',
            'created_by',
            'update_by',
        ],
    ]) ?>

</div>

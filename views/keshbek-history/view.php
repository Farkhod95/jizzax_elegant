<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\KeshbekHistory */
?>
<div class="keshbek-history-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'keshbek',
            'keshbek_sum',
            'cr_date',
            'client_id',
            'order_account_history_id',
        ],
    ]) ?>

</div>

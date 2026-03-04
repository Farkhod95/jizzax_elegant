<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Expenses */
?>
<div class="expenses-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nomi',
            'summa',
            'date_cr',
            'type_id',
            'loss_of_profit_id',
        ],
    ]) ?>

</div>

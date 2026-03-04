<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\LossOfProfit */
?>
<div class="loss-of-profit-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'profit',
            'loss',
            'date_end',
            'date_start',
            'created_by',
        ],
    ]) ?>

</div>

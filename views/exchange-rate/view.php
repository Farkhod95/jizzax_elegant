<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ExchangeRate */
?>
<div class="exchange-rate-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'dollar',
        ],
    ]) ?>

</div>

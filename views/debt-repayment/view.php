<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\DebtRepayment */
?>
<div class="debt-repayment-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'date',
            'text:ntext',
        ],
    ]) ?>

</div>

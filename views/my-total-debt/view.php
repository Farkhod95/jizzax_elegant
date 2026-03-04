<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MyTotalDebt */
?>
<div class="my-total-debt-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cr_date',
            'total_debt',
            'consignor_id',
            'created_by',
            'update_by',
        ],
    ]) ?>

</div>

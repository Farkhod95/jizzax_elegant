<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Check */
?>
<div class="check-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cr_date',
            'cr_date_time',
            'created_by',
            'test:ntext',
            'status',
        ],
    ]) ?>

</div>

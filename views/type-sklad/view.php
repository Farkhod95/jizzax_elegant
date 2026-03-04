<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TypeSklad */
?>
<div class="type-sklad-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
        ],
    ]) ?>

</div>

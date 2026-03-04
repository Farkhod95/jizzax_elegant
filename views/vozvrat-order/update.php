<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\VozvratOrder */
?>
<div class="vozvrat-order-update">

    <?= $this->render('_form', [
        'model' => $model,
        'client_total_debt' => $client_total_debt,
    ]) ?>

</div>
